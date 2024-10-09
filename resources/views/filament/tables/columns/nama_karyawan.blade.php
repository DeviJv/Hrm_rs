 <ul class="list-disc text-sm p-2">
     @foreach ($getState() as $variable => $value)
         <li>{{ $value['nama_karyawan'] }}</li>
     @endforeach
 </ul>
