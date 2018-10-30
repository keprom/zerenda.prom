ВСЕГО:  
<?php
    echo $query->num_rows()." предприятий<br><br>";
	foreach ($query->result() as $row)
	{
	    echo "<tr><td><b>{$row->dogovor}</b></td><td><b>";
		 
		 $link ="<FONT COLOR=\"";
		if ($row->firm_closed=="t")
		{
			 $link.="GRAY";
		}
		else
		{
			if  ($row->is_closed!=NULL)
			{
				$link.= "RED";
			}
			else
			{
				$link.= "GREEN";
			}
		}  
	     $link.="\">   {$row->firm_name}   </FONT></b><br/>";
	   
	   
	   
	   echo anchor("billing/firm/".$row->firm_id,$link)."</td></tr>";
	}
	echo "<br><br><br>".anchor("billing/add_firm","Добавить фирму");
?>
