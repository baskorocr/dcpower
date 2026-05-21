@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<div style="font-size: 24px; font-weight: bold; color: #10b981;">DC Connect</div>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
