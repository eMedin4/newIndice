<table class="develop-data">
        <tr>
            <td><span>{{ $record->movie->year }}</span></td>
            <td>Fa Rat <span>{{ $record->movie->fa_rat }}</span></td>
            <td>Fa Count <span>{{ $record->movie->fa_count }}</span></td>
            <td>Fa step1 <span>{{ $record->movie->fa_popularity["step1"] }}</span></td>
            <td>Fa step2 <span>{{ $record->movie->fa_popularity["step2"] }}</span></td>
            <td>Fa class <span>{{ $record->movie->fa_popularity["class"] }}</span></td>
        </tr>
    @if ($record->movie->im_rat)
        <tr>
            <td><span>{{ $record->movie->year }}</span></td>
            <td>Im Rat <span>{{ $record->movie->im_rat }}</span></td>
            <td>Im Count <span>{{ $record->movie->im_count }}</span></td>
            <td>Im step1 <span>{{ $record->movie->im_popularity["step1"] }}</span></td>
            <td>Im step2 <span>{{ $record->movie->im_popularity["step2"] }}</span></td>
            <td>Im class <span>{{ $record->movie->im_popularity["class"] }}</span></td>
        </tr>
    @endif
        <tr>
            <td colspan="2">Rel Fa-Im <span>{{ $record->movie->relation_fa_im }}</span></td>
        </tr>
</table>
<table class="develop-data">
    <tr>
        <td>Fecha <span>{{ $record->time }}</span></td>
        <td>Format </span>{!! $record->format_time !!}</span></td>
        <td>Tramo <span>{{ $record->dayParting['help'] }}</span></td>
    </tr>
    <tr>
        <td>Total <span>{{ $record->sort_coeficient }}</span></td>
    </tr>
</table>