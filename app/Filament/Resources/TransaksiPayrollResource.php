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
use Filament\Tables\Table;
use App\Models\Tidak_masuk;
use App\Models\TransaksiPayroll;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action as FAction;
use App\Filament\Resources\TransaksiPayrollResource\Pages;
use App\Filament\Resources\TransaksiPayrollResource\RelationManagers;

class TransaksiPayrollResource extends Resource
{
    protected static ?string $model = TransaksiPayroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (filled($state) && filled($get('created_at'))) {
                                    $karyawan = Karyawan::where('id', $state)->first();
                                    $pengaturan_payroll = Payroll::where('karyawan_id', $state)->first();
                                    if (empty($pengaturan_payroll)) {
                                        $set('harga_lembur', '');
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
                                    $set('transport', $pengaturan_payroll->transport);
                                    $set('gaji_pokok', $pengaturan_payroll->gaji_pokok);
                                    $set('makan', $pengaturan_payroll->makan);
                                    $set('sub_total_1', ($pengaturan_payroll->gaji_pokok + $pengaturan_payroll->transport + $pengaturan_payroll->makan));
                                    $get_piutang = Piutang::where('karyawan_id', $get('karyawan_id'))->whereMonth('created_at', '=', date('m', strtotime($get('created_at'))))
                                        ->where('status', 'UNPAID')->first();
                                    $get_koperasi = Koperasi::where('karyawan_id', $get('karyawan_id'))->whereMonth('created_at', '=', date('m', strtotime($get('created_at'))))
                                        ->where('status', 'UNPAID')->first();
                                    $total_lembur = Lembur::where('karyawan_id', $get('karyawan_id'))->whereMonth('tgl_lembur', '=', date('m', strtotime($get('created_at'))))
                                        ->sum('total_lembur');

                                    $tenor_koperasi = explode(" ", $get_koperasi->tenor);
                                    $set('koperasi', $get_koperasi->tagihan / $tenor_koperasi[0]);
                                    $set('piutang', $get_piutang->sub_total);
                                    $set('lembur', round(ceil($total_lembur), PHP_ROUND_HALF_UP));
                                }
                            }),
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
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),
                        Fieldset::make('Sub Total 2')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 5])
                            ->schema([
                                TextInput::make('penyesuaian')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('insentif')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('fungsional')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('fungsional_it')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('jabatan')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('sub_total_2')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->required()
                                    ->suffixAction(
                                        FAction::make('hitung')
                                            ->icon('heroicon-m-arrow-path')
                                            ->requiresConfirmation()
                                            ->action(function (Set $set, Get $get, $state) {
                                                $hitung = $get('sub_total_1') + $get('penyesuaian') + $get('insentif') + $get('fungsional') + $get('fungsional_it') + $get('jabatan');
                                                $set('sub_total_2', $hitung);
                                                $get_absensi = Tidak_masuk::where('karyawan_id', $get('karyawan_id'))->where('keterangan', 'izin')
                                                    ->orWhere('keterangan', 'tanpa_keterangan')
                                                    ->whereMonth('tgl_mulai', '=', date('m', strtotime($get('created_at'))))->count();
                                                $get_tgl_terakhir = Carbon::parse($get('created_at'))->endOfMonth();
                                                $set('tidak_masuk', ($hitung / $get_tgl_terakhir->day) * $get_absensi);
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
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('ketenagakerjaan')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('pajak')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),

                        Fieldset::make('Potongan Karyawan')
                            ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                            ->schema([
                                TextInput::make('tidak_masuk')
                                    ->label('Izin')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('piutang')
                                    ->label('Piutang Obat & Catering')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                                TextInput::make('koperasi')
                                    ->label('Koperasi')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('Rp '),
                            ]),

                        TextInput::make('lembur')
                            ->label('Total Lembur')
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('total')
                            ->label('Total Payroll')
                            ->readOnly()
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
