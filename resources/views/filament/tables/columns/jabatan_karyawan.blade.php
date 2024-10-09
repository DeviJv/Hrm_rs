 <ul class="list-disc text-sm p-2">
     @foreach ($getState() as $variable => $value)
         <li>{{ $value['jabatan_karyawan'] }}</li>
     @endforeach
 </ul>
