<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Lembur;
use App\Models\Payroll;
use App\Models\Piutang;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Karyawan;
use App\Models\Koperasi;
use Filament\Forms\Form;
use App\Models\Perusahaan;
use Filament\Tables\Table;
use App\Models\Tidak_masuk;
use App\Models\PengaturanPajak;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TransaksiPayroll;
use Filament\Resources\Resource;
use App\Models\PembayaranPiutang;
use App\Models\PembayaranKoperasi;
use Illuminate\Support\HtmlString;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\PiutangResource;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\TransaksiPayrollExporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action as FAction;
use App\Filament\Exports\TransaksiPayrollWithBankExporter;
use App\Filament\Resources\TransaksiPayrollResource\Pages;
use App\Filament\Resources\TransaksiPayrollResource\RelationManagers;

class TransaksiPayrollResource extends Resource
{
    protected static ?string $model = TransaksiPayroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 4, 'lg' => 4])
                    // ->description(new HtmlString('Note : untuk <b>HARGA LEMBUR</b> di dapat dari penjumlahan <b>(GAJI POKO + TRANSPORT + MAKAN)</b> pada menu pengaturan payroll'))
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Tanggal Transaksi Payroll')
                            ->required(),
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->required()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            // ->unique(Lembur::class, 'karyawan_id', ignoreRecord: true)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get, $operation) {
                                if (filled($state) && filled($get('created_at'))) {
                                    $karyawan = Karyawan::where('id', $state)->first();
                                    $pengaturan_payroll = Payroll::where('karyawan_id', $state)->first();
                                    if (empty($pengaturan_payroll)) {
                                        $set('harga_lembur', '');
                                        $set('karyawan_id', '');
                                        return
                                            Notification::make()
                                            ->title("Ops Sepertinya <b>{$karyawan->nama}</b> Belum Punya Pengaturan Gaji Poko,dll ")
                                            ->body('Silahkan klik buat Untuk membuat pengaturan')
                                            ->warning()
                                            ->actions([
                                                Action::make('buat_pengaturan_payroll')
                                                    ->url(PengaturanPayrollResource::getUrl('create'))
                                                    ->openUrlInNewTab(),
                                            ])
                                            ->send();
                                    }
                                    if (!empty($karyawan->bank)) {
                                        if (strtolower($karyawan->bank) == "bri") {
                                            $set('payment_method', 'transfer');
                                        } else {
                                            $set('payment_method', 'transfer_non_bri');
                                        }
                                    } else {
                                        $set('payment_method', 'tunai');
                                    }
                                    $set('transport', $pengaturan_payroll->transport);
                                    $set('gaji_pokok', $pengaturan_payroll->gaji_pokok);
                                    $set('makan', $pengaturan_payroll->makan);
                                    $set('insentif', $pengaturan_payroll->insentif ? $pengaturan_payroll->insentif : 0);
                                    $set('fungsional', $pengaturan_payroll->fungsional);
                                    $set('fungsional_it', $pengaturan_payroll->fungsional_it);
                                    $set('jabatan', $pengaturan_payroll->tunjangan);
                                    $set('bpjs_kesehatan', $pengaturan_payroll->bpjs_kesehatan);
                                    $set('ketenagakerjaan', $pengaturan_payroll->bpjs_ketenagakerjaan);
                                    $set('sub_total_1', ($pengaturan_payroll->gaji_pokok + $pengaturan_payroll->transport + $pengaturan_payroll->makan));
                                    $get_piutang = Piutang::where('karyawan_id', $get('karyawan_id'))->whereMonth('created_at', '=', date('m', strtotime($get('created_at'))))
                                        ->where('status', 'UNPAID')->first();
                                    $get_koperasi = Koperasi::where('karyawan_id', $get('karyawan_id'))->where('status', 'UNPAID')->first();
                                    $total_lembur = Lembur::where('karyawan_id', $get('karyawan_id'))
                                        ->whereMonth('tgl_lembur', '=', date('m', strtotime($get('created_at'))))
                                        // ->where('status', 'approved')
                                        ->sum('total_lembur');
                                    if ($operation == "create") {
                                        if (!empty($get_piutang)) {
                                            $set('piutang', $get_piutang->sub_total);
                                        }
                                        if (!empty($get_koperasi)) {
                                            $tenor_koperasi = explode(" ", $get_koperasi->tenor);
                                            $pembayaran_koperasi = PembayaranKoperasi::where('koperasi_id', $get_koperasi->id)->sum('nominal');
                                            if ($pembayaran_koperasi == 0) {
                                                $tagihan = $get_koperasi->tagihan / $tenor_koperasi[0];
                                                $get_bunga =  ($get_koperasi->tagihan / 100) * 2;
                                                $set('koperasi', $get_bunga + $tagihan);
                                            } else {
                                                $set('koperasi', $get_koperasi->tagihan / $tenor_koperasi[0]);
                                            }
                                        }
                                    }

                                    $set('lembur', round(ceil($total_lembur), PHP_ROUND_HALF_UP));
                                }
                            }),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->searchable()
                            ->required()
                            ->options([
                                "tunai" => 'tunai',
                                "transfer" => 'transfer',
                                "transfer_non_bri" => 'transfer non bri',
                            ])
                            ->hintAction(
                                FAction::make('ubah_bank')
                                    ->label('Ubah Bank?')
                                    ->icon('heroicon-o-arrow-path')
                                    ->url(function (Get $get, $state) {
                                        if (filled($get('karyawan_id'))) {
                                            return KaryawanResource::getUrl('edit', ['record' => Karyawan::where('id', $get('karyawan_id'))->first()]);
                                        }
                                    }, $shouldOpenInNewTab = true),
                                // ->action(function (Set $set, $state) {
                                //     $set('price', $state);
                                // })
                            ),
                        Fieldset::make('Sub Total 1')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                            ->schema([
                                // TextInput::make('tunjangan')
                                //     ->readOnly()
                                //     ->required()
                                //     ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                //     ->prefix('Rp '),
                                TextInput::make('gaji_pokok')
                                    ->readOnly()
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('makan')
                                    ->readOnly()
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('transport')
                                    ->readOnly()
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('sub_total_1')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->required()
                                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                                        $component->state($get('gaji_pokok') + $get('makan') + $get('transport'));
                                    })
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),
                        Fieldset::make('Sub Total 2')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 5])
                            ->schema([
                                TextInput::make('penyesuaian')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->default(0)
                                    ->prefix('Rp '),
                                TextInput::make('insentif')
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('fungsional')
                                    ->label('Fungsional Umum')
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('fungsional_it')
                                    ->label('Fungsional Khusus')
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('jabatan')
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('sub_total_2')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->required()
                                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                                        $component->state($get('sub_total_1') + $get('penyesuaian') + $get('insentif') + $get('fungsional')
                                            + $get('fungsional') + $get('fungsional_it') + $get('jabatan'));
                                    })
                                    ->suffixAction(
                                        FAction::make('hitung_2')
                                            ->icon('heroicon-m-arrow-path')
                                            ->requiresConfirmation()
                                            ->modalHeading('Jumlahkan (SUB TOTAL 1 + PENYESUAIAN + INSENTIF + FUNGSIONAL UMUM + FUNGSIONAL KHUSUS + JABATAN)')

                                            ->action(function (Set $set, Get $get, $state) {
                                                $hitung = (int)$get('sub_total_1') + (int)$get('penyesuaian') + (int)$get('insentif') + $get('fungsional') + $get('fungsional_it') + $get('jabatan');
                                                $set('sub_total_2', $hitung);
                                                $set('total', '');
                                                $get_absensi = Tidak_masuk::where('karyawan_id', $get('karyawan_id'))->where('keterangan', 'izin')
                                                    ->whereMonth('tgl_mulai', '=', date('m', strtotime($get('created_at'))))
                                                    ->where('status', 'approved')
                                                    ->sum('jumlah_hari');
                                                $get_tgl_terakhir = Carbon::parse($get('created_at'))->endOfMonth();
                                                $set('tidak_masuk', ($hitung / $get_tgl_terakhir->day) * $get_absensi);
                                                $pajak = PengaturanPajak::where('karyawan_id', $get('karyawan_id'))->first();
                                                if ($pajak != null) {
                                                    $hitung_pajak = ($hitung * $pajak->tarif) / 100;
                                                    $set('pajak', $hitung_pajak);
                                                } else {
                                                    $set('pajak', 0);
                                                }
                                            })
                                    )
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),

                        Fieldset::make('Kewajiban Karyawan')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                            ->schema([
                                TextInput::make('bpjs_kesehatan')
                                    ->required()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('ketenagakerjaan')
                                    ->required()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('pajak')
                                    ->default(0)
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('sub_total_3')
                                    ->label('Sub Total Kewajiban Karyawan')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->required()
                                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                                        $component->state(
                                            $get('pajak') + $get('bpjs_kesehatan') + $get('ketenagakerjaan')
                                        );
                                    })
                                    ->suffixAction(
                                        FAction::make('hitung_3')
                                            ->icon('heroicon-m-arrow-path')
                                            ->requiresConfirmation()
                                            ->modalHeading('Jumlahkan (BPJS KESEHATAN + BPJS KETENAGAKERJAAN + PAJAK)')

                                            ->action(function (Set $set, Get $get, $state) {
                                                $hitung = (int)$get('bpjs_kesehatan') + (int)$get('ketenagakerjaan') + (int)$get('pajak');
                                                $set('sub_total_3', $hitung);
                                                $set('total', '');
                                            })
                                    )
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),

                        Fieldset::make('Potongan Karyawan')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                            ->schema([
                                TextInput::make('tidak_masuk')
                                    ->label('Izin')
                                    ->required()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp ')
                                    ->hintAction(
                                        FAction::make('check_izin')
                                            ->label('Cek Izin?')
                                            ->tooltip('Jangan Lupa Approve Di Pengajuan izin/cuti')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->url(function (Get $get, $state) {
                                                if (filled($get('karyawan_id'))) {
                                                    $bulan = Carbon::parse($get('created_at'));
                                                    $tgl_pertama = $bulan->startOfMonth()->toDateString();
                                                    $tgl_akhir = $bulan->endOfMonth()->toDateString();
                                                    return TidakMasukResource::getUrl('index', ["&tableFilters[created_at][karyawan_id]={$get('karyawan_id')}&tableFilters[created_at][keterangan]=izin&tableFilters[created_at][created_from]={$tgl_pertama}&tableFilters[created_at][created_until]={$tgl_akhir}"]);
                                                }
                                            }, $shouldOpenInNewTab = true),
                                    ),
                                TextInput::make('piutang')
                                    ->label('Piutang Obat & Catering')
                                    ->required()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp ')
                                    ->readOnly(function ($operation) {
                                        if ($operation == "edit") {
                                            return true;
                                        }
                                        return false;
                                    })
                                    ->hintAction(
                                        FAction::make('check_piutang')
                                            ->label('Cek Piutang?')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->url(function (Get $get, $state) {
                                                if (filled($get('karyawan_id'))) {
                                                    $bulan = Carbon::parse($get('created_at'));
                                                    $tgl_pertama = $bulan->startOfMonth()->toDateString();
                                                    $tgl_akhir = $bulan->endOfMonth()->toDateString();
                                                    return PiutangResource::getUrl('index', ["&tableFilters[created_at][karyawan_id]={$get('karyawan_id')}&tableFilters[created_at][status]=UNPAID&tableFilters[created_at][created_from]={$tgl_pertama}&tableFilters[created_at][created_until]={$tgl_akhir}"]);
                                                }
                                            }, $shouldOpenInNewTab = true),
                                    ),
                                TextInput::make('koperasi')
                                    ->label('Koperasi')
                                    ->required()
                                    ->default(0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp ')
                                    ->readOnly(function ($operation) {
                                        if ($operation == "edit") {
                                            return true;
                                        }
                                        return false;
                                    })
                                    ->hintAction(
                                        FAction::make('check_koperasi')
                                            ->label('Cek Koperasi?')
                                            ->icon('heroicon-o-magnifying-glass')

                                            ->url(function (Get $get, $state) {
                                                if (filled($get('karyawan_id'))) {
                                                    $bulan = Carbon::parse($get('created_at'));
                                                    $tgl_pertama = $bulan->startOfMonth()->toDateString();
                                                    $tgl_akhir = $bulan->endOfMonth()->toDateString();
                                                    return KoperasiResource::getUrl('index', ["&tableFilters[created_at][karyawan_id]={$get('karyawan_id')}&tableFilters[created_at][status]=UNPAID&tableFilters[created_at][created_from]={$tgl_pertama}&tableFilters[created_at][created_until]={$tgl_akhir}"]);
                                                }
                                            }, $shouldOpenInNewTab = true),
                                    ),
                                TextInput::make('biaya_admin')
                                    ->label('Biaya admin transfer non BRI')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(2900)
                                    ->afterStateHydrated(function (TextInput $component) {
                                        $component->state(2900);
                                    })
                                    ->prefix("Rp ")
                                    ->hidden(function (Get $get) {
                                        if ($get('payment_method') == "transfer_non_bri") {
                                            return false;
                                        }
                                        return true;
                                    })
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),
                                TextInput::make('sub_total_4')
                                    ->label('Sub Total Potongan Karyawan')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->required()
                                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                                        $hitung = $get('tidak_masuk') + $get('piutang') + $get('koperasi');
                                        // if ($get('payment_method') == "transfer_non_bri") {
                                        //     $hitung = $hitung + $get('biaya_admin');
                                        // }
                                        $component->state(
                                            $hitung
                                        );
                                    })
                                    ->suffixAction(
                                        FAction::make('hitung_4')
                                            ->icon('heroicon-m-arrow-path')
                                            ->requiresConfirmation()
                                            ->modalHeading('Jumlahkan (IZIN + PIUTANG OBAT & CATERING + KOPERASI)')

                                            ->action(function (Set $set, Get $get, $state) {
                                                $hitung = (int)$get('tidak_masuk') + (int)$get('piutang') + (int)$get('koperasi');

                                                $set('sub_total_4', $hitung);
                                                $set('total', '');
                                            })
                                    )
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),

                        TextInput::make('lembur')
                            ->label('Total Lembur')
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->hintAction(
                                FAction::make('check_lembur')
                                    ->label('Cek Lembur?')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->url(function (Get $get, $state) {
                                        if (filled($get('karyawan_id'))) {
                                            $bulan = Carbon::parse($get('created_at'));
                                            $tgl_pertama = $bulan->startOfMonth()->toDateString();
                                            $tgl_akhir = $bulan->endOfMonth()->toDateString();
                                            return LemburResource::getUrl('index', ["&tableFilters[created_at][karyawan_id]={$get('karyawan_id')}&tableFilters[created_at][created_from]={$tgl_pertama}&tableFilters[created_at][created_until]={$tgl_akhir}"]);
                                        }
                                    }, $shouldOpenInNewTab = true),
                            ),
                        TextInput::make('total')
                            ->label('Total Payroll')
                            ->readOnly()
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->suffixAction(
                                FAction::make('hitung_4')
                                    ->icon('heroicon-m-arrow-path')
                                    ->requiresConfirmation()
                                    ->modalHeading('Jumlahkan (SUB TOTAL 2 - SUB TOTAL KEWAJIBAN KARYAWAN & SUB TOTAL POTONGAN KARYAWAN)')

                                    ->action(function (Set $set, Get $get, $state) {
                                        $hitung = (int)$get('sub_total_2') - (int)$get('sub_total_3') - (int)$get('sub_total_4');
                                        if ($get('payment_method') == "transfer_non_bri") {
                                            $hitung = $hitung - 2900;
                                        }
                                        $set('total', $hitung);
                                    })
                            ),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            // ->defaultGroup('payment_method')
            ->groups([
                Group::make('payment_method')
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->sortable()
                    ->date(),
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_method'),
                TextColumn::make('gaji_pokok')
                    ->toggleable()
                    ->money('IDR'),
                TextColumn::make('makan')
                    ->toggleable()
                    ->money('IDR'),
                TextColumn::make('insentif')
                    ->toggleable()
                    ->money('IDR'),
                TextColumn::make('transport')
                    ->toggleable()
                    ->money('IDR'),
                TextColumn::make('jabatan')
                    ->toggleable()
                    ->money('IDR'),
                TextColumn::make('penyesuaian')
                    ->money('IDR'),
                TextColumn::make('fungsional')
                    ->label('Fungsional Umum')
                    ->money('IDR'),
                TextColumn::make('fungsional_it')
                    ->label('Fungsional Khusus')
                    ->money('IDR'),
                TextColumn::make('bpjs_kesehatan')
                    ->money('IDR'),
                TextColumn::make('ketenagakerjaan')
                    ->money('IDR'),
                TextColumn::make('koperasi')
                    ->money('IDR'),
                TextColumn::make('pajak')
                    ->money('IDR'),
                TextColumn::make('tidak_masuk')
                    ->label('Izin')
                    ->money('IDR'),
                TextColumn::make('piutang')
                    ->money('IDR'),
                TextColumn::make('lembur')
                    ->money('IDR'),
                TextColumn::make('total')
                    ->summarize(Sum::make()->money('IDR'))
                    ->money('IDR'),
            ])
            ->filters([
                SelectFilter::make('karyawan_id')
                    ->label('Karyawan')
                    ->preload()
                    ->searchable()
                    ->multiple()
                    ->relationship('karyawan', 'nama'),
                Tables\Filters\Filter::make('created_at')
                    ->form([

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->searchable()
                            ->required()
                            ->options([
                                "tunai" => 'tunai',
                                "transfer" => 'transfer',
                                "transfer_non_bri" => 'transfer non bri',
                            ]),
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Tanggal Mulai')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Tanggal Akhir')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query

                            ->when(
                                $data['payment_method'] ?? null,
                                fn(Builder $query, $data): Builder => $query->where('payment_method', '=', $data),
                            )
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['payment_method'] ?? null) {
                            $indicators['payment_method'] = 'Payment Method : ' . $data['payment_method'];
                        }
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Tanggal Mulai : ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Tanggal Akhir : ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cetak_pdf')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records) {
                            $perusahaan = Perusahaan::first();

                            $records = $records->toQuery()->with('karyawan.tidak_masuks', fn($q) => $q->where('keterangan', 'izin'))->get();
                            $data = [
                                'records' => $records,
                                'perusahaan' => $perusahaan,
                                // 'perusahaan' => $perusahaan
                            ];
                            session()->put('slip_gaji', $data);
                            return redirect()->route('pdf.slip_gaji');
                            // $pdf = Pdf::loadView('pdf.slip_gaji', $data);
                            // return response()->streamDownload($pdf->download('slip_gaji.pdf'));
                            // return response()->streamDownload(function () use ($data) {
                            //     echo Pdf::loadHtml(
                            //         Blade::render('pdf.slip_gaji', $data)
                            //     )->stream();
                            // }, 'slip_gaji.pdf');
                        })
                        ->label('Cetak Slip Gaji'),
                    ExportBulkAction::make('export_payroll')
                        ->color('primary')
                        ->label('Export Payroll')
                        ->modifyQueryUsing(fn($query) => $query->reorder()->orderBy('created_at', 'desc'))
                        ->exporter(TransaksiPayrollExporter::class),
                    ExportBulkAction::make('export_payroll_with_bank')
                        ->color('primary')
                        ->label('Export Payroll With Bank')
                        ->exporter(TransaksiPayrollWithBankExporter::class)
                        ->fileName(fn(Export $export): string => "Transaksi Payroll With Bank-{$export->getKey()}"),
                    Tables\Actions\DeleteBulkAction::make()
                        ->databaseTransaction()
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('password')
                                ->required()
                                ->password()
                                ->currentPassword()
                        ])
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->piutang > 0) {
                                    $get_piutang = Piutang::where('karyawan_id', $record->karyawan_id)
                                        ->whereMonth('created_at', '=', date('m', strtotime($record->created_at)))->first();
                                    $pembayaran_piutang = PembayaranPiutang::where('piutang_id', $get_piutang->id)
                                        ->whereDate('created_at', '=', $record->created_at)->delete();
                                    $get_piutang->status = "UNPAID";
                                    $get_piutang->save();
                                }
                                if ($record->koperasi > 0) {
                                    $koperasi = Koperasi::where('karyawan_id', $record->karyawan_id)
                                        ->whereMonth('created_at', '=', date('m', strtotime($record->created_at)))->first();
                                    if ($koperasi != null) {
                                        $pembayaran_koperasi = PembayaranKoperasi::where('koperasi_id', $koperasi->id)
                                            ->whereDate('created_at', '=', $record->created_at)->delete();
                                        $koperasi->status = "UNPAID";
                                        $koperasi->save();
                                    }
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPayrolls::route('/'),
            'create' => Pages\CreateTransaksiPayroll::route('/create'),
            'edit' => Pages\EditTransaksiPayroll::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }
}
