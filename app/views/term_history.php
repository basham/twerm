
<table cellspacing="0" cellpadding="0">

	<thead>
		<th>Date</th>
		<th class="number">Rank</th>
		<th class="number">Power Rank</th>
		<th class="number">Mentions</th>
		<th class="number">Total Mentions</th>
	</thead>
	
	<tbody>
<?php

$totalMentions = array();
$powerRanks = array();

for( $i = count($time_period_terms) - 1; $i >= 0; $i-- ) {
	$count = $time_period_terms[$i]->count;
	$prev_count = array_key_exists( $i + 1, $totalMentions ) ? $totalMentions[$i + 1] : 0;
	$totalMentions[ $i ] = $count + $prev_count;
	
	$rank = $time_period_terms[$i]->rank;
	$powerRanks[$i] = array_key_exists( $i + 1, $time_period_terms ) ? $time_period_terms[$i + 1]->rank - $rank : '-';
}

$i = 0;

foreach( $time_period_terms as $term ) {
	
	echo "\t\t<tr".( $i % 2 == 0 ? ' class="alt"' : '').">";

	echo "\n\t\t\t<td><a href=\"".$term->getTimePeriod()->getURL()."\">".$term->getTimePeriod()->getDate()."</a></td>";
	echo "\n\t\t\t<td class=\"number\">".$term->rank."</td>";
	echo "\n\t\t\t<td class=\"number\">".( $powerRanks[$i] )."</td>";
	echo "\n\t\t\t<td class=\"number\"><a href=\"".$term->getURL()."\">".$term->count."</a></td>";
	echo "\n\t\t\t<td class=\"number\">".$totalMentions[$i]."</td>";

	echo "\n\t\t</tr>";
	
	echo "\n";
	
	$i++;
}

?>
	</tbody>

</table>
