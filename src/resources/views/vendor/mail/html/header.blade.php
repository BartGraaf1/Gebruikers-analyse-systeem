@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://www.pingvp.com/web/wp-content/uploads/2022/04/pingvp-color.svg" class="logo" alt="PingVP Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
