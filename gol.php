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
	foreach ($matrix as $cord_y => $matrix_row) {
		foreach ($matrix_row as $cord_x => $matrix_block)
		{
			$nb_num = block_get_neighbour_count($matrix,$cord_x,$cord_y);
			if ($matrix_block == false) // halott a jelenlegi blokk
			{
				if ($nb_num == 3) // 3 szomszed -> ujjaeled a halott blokk
				{
					$new_val = true;
				}
				else
				{
					$new_val = false;
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
			$matrix_next[$cord_y][$cord_x] = $new_val;
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
			if ($matrix[$y][$x] == true)
			{
				if (!(($y==$m_y) && ($x==$m_x)))
				{
					$neighbour_living++;
				}
			}
		}
	}
	return $neighbour_living;
}

function load_lif ($life_file_path, $matrix_size_x, $matrix_size_y) // .lif file betoltese a meghatarozott meretu matrixra
{
	$matrix = array();
	$cord_x_start = $cord_x = intval($matrix_size_x / 2);
	$cord_y_start = $cord_y = intval($matrix_size_y / 2);

	for ($x=0;$x<$matrix_size_x;$x++)
	{
		for ($y=0;$y<$matrix_size_y;$y++)
		{
			$matrix[$x][$y] = false;
		}

	}

	$fp = @fopen($life_file_path,'r');
	while ($sor = @fgets($fp))
	{
		$sordat = explode(' ',$sor);
		if ($sordat[0] == '#P') // uj pozicio
		{
			$cord_x = $cord_x_start + intval($sordat[1]);
			$cord_y = $cord_y_start + intval($sordat[2]);
		}
		elseif ((substr($sordat[0],0,1)=='.') || (substr($sordat[0],0,1)=='*')) // pont berakasa
		{
			$p_len = strlen($sordat[0]);
			for ($p=0;$p<$p_len;$p++)
			{
				if (substr($sordat[0],$p,1) == '*')
				{
					$matrix[$cord_y][$cord_x + $p] = true;
					//echo ($cord_x+$p).','.($cord_y).'<br>';
				}
			}
			$cord_y++;
		}
	}
	@fclose($fp);

	return $matrix;
}

function matrix_to_array ($matrix) // egydimenzios arrayba toltom az adatokat
{
	$matrix_array = array();
	foreach ($matrix as $cord_y => $matrix_row)
	{
		foreach ($matrix_row as $cord_x => $matrix_block)
		{
			if ($matrix_block)
			{
				$matrix_array[] = $cord_x;
				$matrix_array[] = $cord_y;
			}
		}
	}
	return $matrix_array;
}


// programtorzs

if ($_GET['reset']=='1') // ujrainditas
{
	unlink('gol.json');
}

if (file_exists('gol.json')) // ha van folyamatban levo allapot
{
	$matrix = json_decode(file_get_contents('gol.json'));
}
else
{
	if ($_GET['lif_url']!='')
	{
		$matrix = load_lif($_GET['lif_url'], 50, 50);
	}
	else
	{
		$matrix = load_lif('http://radicaleye.com/lifepage/patterns/acorn.lif', 50, 50);
		//$matrix = load_lif('test.lif',50,50);
	}
}

$matrix = generation_increase($matrix); // kovetkezo fazis
$array_to_draw = matrix_to_array($matrix);

// html valtozok feltoltese majd kiirasa a kirajzolando tommel
$tpl = file_get_contents('gol.html');
$tpl = str_replace('{{ draw_array }}',implode(',',$array_to_draw),$tpl);
$tpl = str_replace('{{ lif_url }}',$_GET['lif_url'],$tpl);
echo $tpl;

file_put_contents('gol.json',json_encode($matrix)); // allapot tarolasa JSON-be

// programtorzs vege

?>