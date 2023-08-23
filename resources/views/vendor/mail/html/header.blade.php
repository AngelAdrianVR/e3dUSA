@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{assets('images/logo.png')}}" class="logo" alt="emblems3d usa logo">
@else
{{ $slot }}
<img src="{{asset('images/logo.png')}}" class="logo" alt="emblems3d usa logo">
@endif
</a>
</td>
</tr>
