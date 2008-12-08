
<table>

	<thead>
		<th>Date</th>
		<th>Rank</th>
		<th>Power Rank</th>
		<th>Mentions</th>
		<th>Total Mentions</th>
	</thead>
	
	<tbody>
<?php

foreach( $time_period_terms as $term ) {
	
	echo "\t\t<tr>";

	echo "\n\t\t\t<td><a href=\"".$term->getTimePeriod()->getURL()."\">".$term->getTimePeriod()->getDate()."</a></td>";
	echo "\n\t\t\t<td>".$term->rank."</td>";
	echo "\n\t\t\t<td>".$term->power_rank."</td>";
	echo "\n\t\t\t<td><a href=\"".$term->getURL()."\">".$term->count."</a></td>";
	echo "\n\t\t\t<td>".$term->count."</td>";

	echo "\n\t\t</tr>";
	
	echo "\n";
	
}

?>
	</tbody>

</table>
