<?php
/**
 * Created by PhpStorm.
 * User: inf
 * Date: 2017.02.01.
 * Time: 10:52
 */

function generation_increase ($matrix) // generacio leptetes
{
	$matrix_next = $matrix;
	foreach ($matrix as $cord_x => $matrix_row) {
		foreach ($matrix_row as $cord_y => $matrix_block)
		{
			$nb_num = block_get_neighbour_count($matrix,$cord_x,$cord_y);
			if ($matrix_block == false) // halott a jelenlegi blokk
			{
				if ($nb_num == 3) // 3 szomszed -> ujjaeled a halott blokk
				{
					$new_val = true;
				}
			}
			else // elo a jelenlegi blokk
			{
				if (($nb_num < 2) ||  ($nb_num > 3)) //  2-nel kevesebb vagy 3-nal tobb szomszed -> meghal a blokk
				{
					$new_val = false;
				}
				else
				{
					$new_val = true;
				}
			}
			$matrix_next[$cord_x][$cord_y] = $new_val;
		}
	}
	return $matrix_next;
}

function block_get_neighbour_count ($matrix, $m_x, $m_y) // a szomszedok szamat adja vissza
{
	$neighbour_living = 0;
	for ($x=$m_x-1;$x<($m_x+2);$x++)
	{
		for ($y=$m_y-1;$y<($m_y+2);$y++)
		{
			if ($matrix[$x][$y] == true)
			{
				$neighbour_living++;
			}
		}
	}
	return $neighbour_living;
}


?>