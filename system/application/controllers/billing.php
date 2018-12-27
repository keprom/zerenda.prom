<?php
class Billing extends Controller
{
	function dob($text)
	{$d=($text);
	return str_pad($d,7,'0', STR_PAD_LEFT);
	}
	
	function datetostring($date)
	{
		$d=explode("-",$date); 
		return $d['2'].'.'.$d['1'].'.'.$d['0'];
	}
	
	function shortdate($date)
	{
		$d=explode("-",$date); 
		return $d['1'].'.'.substr($d['0'],2,2);
	}
	
	function f_d_graph($var)
	{
		if ($var==0) return "0"; else
		return trim(sprintf("%22.2f",$var));
	}
	
	function d2($date)
	{
		$d=explode("-",$date); 
		return $d['2'].'.'.$d['1'].'.'.substr($d['0'],2,2);
	}
	function execute($function)
	{
		if (($this->session->userdata('billing/'.$function)=='t') or 
		($this->session->userdata('login')=='programmist') or ($this->session->userdata('login')=='admin'))
			eval('$this->'.$function.'();');
	}
	function Billing()
	{
		parent::Controller();
		set_time_limit(0);

        $is_login = $this->session->userdata('is_login');

        if ($is_login!=TRUE)
        {
            redirect("login/billing");
            die();
        }

		$class_method=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $user_id = $this->session->userdata('id');
        date_default_timezone_set('Asia/Almaty');

        $action = array(
            "user_id" => $user_id,
            "class_method" => $class_method,
            "action_time" => date("Y-m-d H:i:s"),
            "ip_address" => $_SERVER['REMOTE_ADDR']
        );

        $first_arg = $this->uri->segment(3);
        $second_arg = $this->uri->segment(4);

        if (!empty($first_arg)) {
            $action['first_arg'] = $first_arg;
            if (!empty($second_arg)) {
                $action['second_arg'] = $second_arg;
            }
        }

        $this->db->insert("industry.user_action", $action);
		if ($class_method=='/') redirect("billing");

		if (($this->session->userdata('login')=='programmist') or ($this->session->userdata('login')=='admin'))
		{
			if (($class_method!='billing/jpeg') and ($this->session->userdata('admin')=='programmist'))
			$this->output->enable_profiler(TRUE);
		}
		else
		{
			$class_method=$this->uri->segment(1).'/'.$this->uri->segment(2);
			if ($this->session->userdata($class_method)!='t')
			{
				if (($class_method=='billing/') or ($class_method=='billing/index') )
					{
						redirect("login");
						die('<h1>Доступ запрещен</h1>');
					}
				redirect("billing");
				die('<h1>Доступ запрещен</h1>');
			}
		}	
	}
	function left()
	{
		#added
		$data['month_to_look'] = $this->db->query("select * from industry.current_period()")->row()->current_period;
		#end of added
		$data['poisk']=$this->session->userdata('poisk');
		if ($this->session->userdata('poisk')==NULL) $data['poisk']='1';
		$this->load->view("left",$data);
		$this->load->view("messages");		
	}
	function phpinfo()
	{ 
		echo phpinfo();
	}

	function index()
	{
		$this->left();
		$this->db->order_by('dogovor');
		$data['query']=$this->db->get("industry.firm_overview");
		$this->load->view("billing_view",$data);
		$this->load->view("right");
	}
	function my_firm()
	{
		$this->left();
		$this->db->where('user_id',$this->session->userdata('id'));
		$this->db->order_by('dogovor');
		$data['query']=$this->db->get("industry.firm_overview");
		$this->load->view("billing_view",$data);
		$this->load->view("right");
	}
	
	function my_firm_not_closed()
	{
		$this->left();
		$this->db->where('is_closed is null',null,FALSE);
		$this->db->where('user_id',$this->session->userdata('id'));
		$this->db->where('firm_closed',"FALSE");
		$this->db->order_by('dogovor');
		$data['query']=$this->db->get("industry.firm_overview");
		$this->load->view("billing_view",$data);
		$this->load->view("right");
	}
	function firm_not_closed()
	{
		$this->left();
		$this->db->where('is_closed is null',null,FALSE);
		$this->db->order_by('dogovor');
		$this->db->where('firm_closed',"FALSE");
		$data['query']=$this->db->get("industry.firm_overview");
		$this->load->view("billing_view",$data);
		$this->load->view("right");
	}


	function firm()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['r']=$this->db->get('industry.firm_view')->row();
		 
		$sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
		$data['period']=$this->db->query($sql);
		$sql="Select industry.is_closed(".$this->uri->segment(3).") as closed";
		$data['is_closed']=$this->db->query($sql)->row();
		$this->left();
		$this->load->view("firm_view",$data);
		$this->execute("points");
		$this->load->view("right");
	}
	function firm_edit()
	{
		$sql = "SELECT firm.id,firm.dogovor,firm.address, firm.name,  firm.telefon, firm.rnn, firm.dogovor_date, firm.bik FROM industry.firm WHERE  firm.id=".$this->uri->segment(3); 
		$this->db->where('id',$this->uri->segment(3));
		$data['r']=$this->db->get('industry.firm');
		 
		$sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
		$data['period']=$this->db->query($sql);
		$this->db->order_by('name');
		$data['firm_subgroup']=$this->db->get('industry.firm_subgroup');
		$this->db->order_by('name');
		$data['bank']=$this->db->get('industry.bank');
		$this->db->order_by('name');
		$data['firm_otrasl']=$this->db->get('industry.firm_otrasl');
		$this->db->order_by('name');
		$data['firm_power_group']=$this->db->get('industry.firm_power_group');
		$this->db->order_by('name');
		$data['ture']=$this->db->get('industry.ture');
		$this->left();
		$this->load->view("firm_edit",$data);
		$this->load->view("right");
	}
	function firm_edition()
	{
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('industry.firm',$_POST);
		redirect("billing/firm/".$this->uri->segment(3));
	}
	function close_firm()
	{
		$sql="SELECT industry.close_firm(".$this->uri->segment(3).")";
		$this->db->query($sql);
		redirect("billing");
	}
	function full_close_firm()
	{
		$sql="update industry.firm set firm_closed= not firm_closed where id =".$this->uri->segment(3);
		$this->db->query($sql);
		redirect("billing/firm/".$this->uri->segment(3));
	}
	function open_firm()
	{
		$sql="SELECT industry.open_firm(".$this->uri->segment(3).")";
		$this->db->query($sql);
		redirect("billing/firm/".$this->uri->segment(3));
	}
	function add_firm()
	{		
		$data['banks']=$this->db->get('industry.bank');
		$data['subgroups']=$this->db->get('industry.firm_subgroup');
		$this->left();
		$this->load->view("add_firm_view",$data);
		$this->load->view("right");
	}
	function adding_firm()
	{
		$_POST['dogovor_date']=$_POST['year'].".".$_POST['month'].".".$_POST['day'];
		unset($_POST['year']);
		unset($_POST['day']);
		unset($_POST['month']);
		$this->db->insert("industry.firm",$_POST);
		$this->index();
	}

	function firm_search_by()
	{
	
		$sql="select distinct firm_id from industry.billing_point_ex where ";	
		if ($_POST['type']!='1') $sql="select  * from industry.firm_overview where firm_id in ( ".$sql;
		$str=$_POST['str'];
		$this->session->set_userdata(array('poisk'=>$_POST['type']));
		if ($_POST['type']=='1')
		{
			$sql.=" dogovor = ".$str;
			$query=$this->db->query($sql);
			if ($query->num_rows()>0)
			{
				redirect("billing/firm/".$query->row()->firm_id);
			}
			else
			{
				redirect("billing/");
			}
		}
		else
		{									
			$arr=explode(" ",$str);
			$first=true;
			if ($_POST['type']==2) {$t="billing_point_address";}
			if ($_POST['type']==3) {$t="firm_address";}
			if ($_POST['type']==4) {$t="rnn";}
			if ($_POST['type']==5) {$t="tp_name";}
			if ($_POST['type']==6) {$t="telefon";}
			if ($_POST['type']==7) {$t="firm_name";}	
			if ($_POST['type'] == 8) {
                $t = "billing_point_name";
            }
            if ($_POST['type'] == 9) {
                $t = "gos_nomer";
            }
            // if ($_POST['type']==10) {$t="firm_bin";}
            if ($_POST['type'] == 10) {
                $t = "firm_bin";
            }
			foreach ($arr as $a)
			{
				trim($a);
				if ($first==FALSE)
				{
					$sql.=" and $t ilike '%$a%' ";
					$first=FALSE;
				}
				if ($first==TRUE)
				{
					$sql.=" $t ilike '%$a%' " ;
					$first=FALSE;
				}
			}
			$sql.=") order by dogovor";
			$this->left();
			$data['query']=$this->db->query($sql);
			$this->load->view("billing_view",$data);
			$this->load->view("right");
		}
	}
	function gd()
	{
		phpinfo();
	}
	//// СПРАВОЧНИКИ
	function streets()
	{
		$this->db->order_by("name");
		$data['query']=$this->db->get("common_info.street");
		$this->left();
		$this->load->view("sprav/streets_view",$data);	
		$this->load->view("right");
	}
	function adding_streets()
	{
		if (trim($_POST['name'])!="")
			$this->db->insert('common_info.street',$_POST);
		redirect("billing/streets");
	}
	function counter_types()
	{
		$this->db->order_by("name");
		$data['query']=$this->db->get("industry.counter_type");
		$this->left();
		$this->load->view("sprav/counter_types_view",$data);	
		$this->load->view("right");	
	}
	function adding_counter_types()
	{
		if (trim($_POST['name'])!="")
			$this->db->insert('industry.counter_type',$_POST);
		redirect("billing/counter_types");
	}
	function tp()
	{
		$this->db->order_by("name");
		$data['query']=$this->db->get("industry.tp");
		$this->left();
		$this->load->view("sprav/tp_view",$data);	
		$this->load->view("right");
		
	}

    function adding_tp()
    {
        if (trim($_POST['name'])!=""){
            $_POST['ture_id'] = isset($_POST['ture_id'])? $_POST['ture_id']: 0;
            $this->db->insert('industry.tp',$_POST);
        }
        redirect("billing/tp");
    }

	function banks()
	{
		$this->db->order_by("name");
		$data['query']=$this->db->get("industry.bank");
		$this->left();
		$this->load->view("sprav/banks_view",$data);	
		$this->load->view("right");
		
	}
	function bank_edit()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['bank_id']=$this->uri->segment(3);
		$data['bank']=$this->db->get('industry.bank')->row();
		$this->left();
		$this->load->view('sprav/bank_edit',$data);
		$this->load->view('right');
	}
	function edition_bank()
	{
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('industry.bank',$_POST);
		redirect('billing/banks');
	}
	function adding_banks()
	{
		$this->db->insert('industry.bank',$_POST);
		redirect("billing/banks");
	}
	///// конец справочники
	/*
	function points()
	{
		 $sql ="select  *  from industry.billing_point_ex 
				          where billing_point_ex.firm_id=".$this->uri->segment(3). " order by billing_point_id";
					 
		$result=$this->db->query($sql);
		if ($result->num_rows()>0)
		{
			$data['result']=$result;
			$this->load->view("points_view",$data);
		}
		else
		{
			echo "Нету точек учета <br><br>";		
		}	
		$this->execute("add_point");	
		
	}
	*/
	
    function points()
    {
        $this->db->where('firm_id', $this->uri->segment(3));
        $result = $this->db->get("industry.point_list");
        if ($result->num_rows() > 0) {
            $data['result'] = $result;
            $this->load->view("points_view", $data);
        } else {
            echo "Нету точек учета <br><br>";
        }
        $this->execute("add_point");
    }	
	
	function add_point()
	{
		$data['firm_id']=$this->uri->segment(3);
		$this->db->order_by('name');
		$data['tps']=$this->db->get('industry.tp');
		$this->load->view("add_billing_point",$data);
	}

    private function check_billing_point($bill_id)
    {
        $bill_id = $this->uri->segment(3);
        $period_id = $this->get_cpi($bill_id);
        $this->db->where("bill_id",$bill_id);
        $this->db->where("period_id",$period_id);
        $n = $this->db->get("industry.nadbavka_info");
        if($n->num_rows > 0){
            die("V dannyi period na tochke ucheta nahodyatsya nadbavki!");
        }
        $this->db->where("bill_id",$bill_id);
        $this->db->where("period_id",$period_id);
        $sbp = $this->db->get("industry.sovm_billing_point");
        if($sbp->num_rows > 0){
            die("Na tochke ucheta imeutsya sovmesntye uchety!");
        }
        $this->db->where("bill_id",$bill_id);
        $unfc = $this->db->get("industry.unfinished_counter");
        if($unfc->num_rows > 0){
            die("Na tochke ucheta imeutsya nesnyatye schetchiki!");
        }
    }

    private function get_cpi(){
        return $this->db->query("select * from industry.current_period_id()")->row()->current_period_id;
    }

	function adding_point()
	{
		$this->db->insert("industry.billing_point",$_POST);
		redirect("billing/firm/".$_POST['firm_id']);
	}

	function point()
	{
		$sql="SELECT * FROM industry.billing_point where id=".$this->uri->segment(3);
		$data['point_data']=$this->db->query($sql)->row();
		$sql="SELECT counter.*,counter_type.name as type from industry.counter,industry.counter_type where counter.type_id=counter_type.id and  point_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($sql);

		$sql="select * from industry.counter where data_start is null  and  point_id=".$this->uri->segment(3);
		$query=$this->db->query($sql);
		$this->left();
		
		if ($query->num_rows()>0) $data['snyat']='yes'; else $data['snyat']='false';
		$this->load->view("counters_view",$data);
		if ($data['snyat']=="false")
			$this->execute("add_counter");
		$this->execute("nadbavka_ot");
		$this->execute("sovm_otn");
		$this->execute("sovm_by_counter");
		$this->load->view("right");
	}
	function break_counter()
	{
		$sql="select * from industry.counter where data_finish is null and point_id=".$_POST['point_id'];
		$query=$this->db->query($sql);

		$data_snyatiya=$_POST['day'].".".$_POST['month'].".".$_POST['year'];
		$sql="update industry.counter set data_finish='".$data_snyatiya."' where id=".$query->row()->id;
		$this->db->query($sql);
		redirect ("billing/point/".$_POST['point_id']);
	}
	function add_counter()
	{
		$sql="SELECT * from industry.counter_type order by name";
		$data['types']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view("add_counter_view",$data);
	}
	function adding_counter()
	{
		$sql="select * from industry.counter where data_finish is  null and  point_id=".$_POST['point_id'];
		$query=$this->db->query($sql);
		if ($query->num_rows()!=0) return;
		$_POST['data_start']=date("Y-m-d", mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
		unset($_POST['year']);
		unset($_POST['day']);
		unset($_POST['month']);
		$this->db->insert("industry.counter",$_POST);
		redirect("billing/point/".$_POST['point_id']);		
	}
	function counter()
	{
		$sql="select values_set.id,tariff.type_name as type from industry.values_set,industry.tariff where tariff.id=values_set.tariff_id and  counter_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($sql);
		$data['counter_id']=$this->uri->segment(3);
		$this->left();
		$this->load->view("counter_view",$data);
		$this->load->view("right");		
	}
	function change_counter()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['counter']=$this->db->get('industry.counter')->row();
		$this->db->order_by('name');
		$data['counter_type']=$this->db->get('industry.counter_type');
		$this->left();
		$this->load->view("counter_edit",$data);
		$this->load->view("right");			
	}
	function changing_counter()
	{
		if ($_POST['data_gos_proverki']=='') $_POST['data_gos_proverki']=NULL;
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('industry.counter',$_POST);
		redirect('billing/counter/'.$this->uri->segment(3));
	}
	function add_sets()
	{
		$sql="select * from industry.counter where data_finish is null and id=".$this->uri->segment(3);
		$query=$this->db->query($sql);
		if ($query->num_rows()==0)  
		{
			echo $this->red("billing/counter/".$this->uri->segment(3));
			return;
		}

		$sql="select * from industry.tariff";
		$data['tariff']=$this->db->query($sql);
		$data['counter_id']=$this->uri->segment(3);
		$this->left();
		$this->load->view("add_sets_view",$data);
		$this->load->view("right");
	}
	function adding_sets()
	{
		$sql="select * from industry.counter where data_finish is null and id=".$_POST['counter_id'];
		$query=$this->db->query($sql);
		if ($query->num_rows()==0)  return;
		$this->db->insert("industry.values_set",$_POST);
		redirect("billing/counter/".$_POST['counter_id']);
	}
	function values_sets()
	{
		$this->left();
		$sql="Select * from industry.counter where id=(select counter_id from industry.values_set where id=".
		$this->uri->segment(3).")";
		$data['counter_data']=$this->db->query($sql)->row();
		$data['sets_id']=$this->uri->segment(3);
		$sql="SELECT tariff_id  from industry.values_set where id=".$this->uri->segment(3);
		$type_id=$this->db->query($sql)->row()->tariff_id;
		$sql="SELECT name from industry.tariff where id=".$type_id;
		$data['sets_type']=$this->db->query($sql)->row()->name;
		$sql="SELECT * from industry.counter_value where values_set_id=".$this->uri->segment(3)."  order by data";
		$data['query']=$this->db->query($sql);
		$this->load->view("values_sets_view",$data);		
		$this->execute("add_pokazanie");
		$this->execute("nadbavka_ab");
		$this->execute("akt");
		$this->execute("sovm_ab");
		$this->load->view("right");
	}
	function add_pokazanie()
	{
		$sql="select * from industry.counter where data_finish is null and id=(select counter_id from industry.values_set where id=".$this->uri->segment(3).")";
		$query=$this->db->query($sql);
		if ($query->num_rows()==0)  return;
		$data['set_id']=$this->uri->segment(3);
		$this->load->view("add_pokazanie_view",$data);	
	}
	function adding_pokazanie()
	{
		$this->session->set_userdata(array('day'=>$_POST['day'],'month'=>$_POST['month'],'year'=>$_POST['year']));
		$data=$_POST['year']."-".$_POST['month']."-".$_POST['day'];
		if (!checkdate($_POST['month'],$_POST['day'],$_POST['year'])) 
		{
			echo  $this->red("billing/values_sets/".$_POST['set_id']);
			return;
		}		
		$_POST['data']=date("Y-m-d", mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
		unset($_POST['year']);
		unset($_POST['day']);
		unset($_POST['month']);	
		$_POST['uroven'] = 0;
		$this->db->insert("industry.counter_value",$_POST);
		redirect("billing/values_sets/".$_POST['values_set_id']);
	}

	function delete_pokazanie()
	{
		$query=$this->db->query("select * from industry.counter_value where id=".$this->uri->segment(3));
		$id=$query->row()->values_set_id;
		$this->db->query("delete from industry.counter_value where id=".$this->uri->segment(3));
		redirect("billing/values_sets/".$id);
	}
	function nadbavka_ot()
	{
		$sql="SELECT * FROM industry.current_ot_nadbavka where billing_point_id=".$this->uri->segment(3);
		$data['nadbavka']=$this->db->query($sql);
		$this->load->view('nadbavka_view',$data);
		$this->execute("add_ot_nadbavka");
	}
	function add_ot_nadbavka()
	{
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('add_nadbavka_view',$data);
	}
	function adding_ot_nadbavka()
	{
		$sql="SELECT value::integer FROM industry.sprav WHERE name='current_period'";
		$a=$this->db->query($sql)->row()->value;
		$_POST['period_id']=$a;
		$this->db->insert("industry.nadbavka_otnositelnaya",$_POST);
		redirect("billing/point/".$_POST['billing_point_id']);
	}

	function nadbavka_ab()
	{
		$sql="SELECT * FROM industry.current_ab_nadbavka where values_set_id=".$this->uri->segment(3);
		$data['nadbavka']=$this->db->query($sql);
		$this->load->view('nadbavka_absolutnaya_view',$data);
		$this->execute("add_ab_nadbavka");
	}
	function add_ab_nadbavka()
	{
		$data['vs_id']=$this->uri->segment(3);
		$this->load->view('add_nadbavka_absolutnaya_view',$data);
	}
	function adding_ab_nadbavka()
	{
		$_POST['tariff_value']=4;
		$this->db->insert("industry.nadbavka_absolutnaya",$_POST);
		redirect("billing/values_sets/".$_POST['values_set_id']);
	}
	
	function sovm_otn()
	{	
		$sql="SELECT * FROM industry.sovm_uchet where child_point_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('sovmestnyy_uchet_view',$data);
		$this->execute("add_sovm_otn");
	}
	function add_sovm_otn()
	{
		$sql='select id,dogovor||\'  \'||name as firm_info from industry.firm  order by dogovor';
		$data['firms']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('add_sovmestnyy_uchet_view',$data);
	}
	function adding_sovm_otn()
	{
		$this->db->insert("industry.sovmestnyy_uchet",$_POST);
		redirect("billing/point/".$_POST['child_point_id']);
	}
	function delete_sovm_otn()
	{
		$query=$this->db->query("select * from industry.sovmestnyy_uchet where id=".$this->uri->segment(3));
		$id=$query->row()->child_point_id;
		$this->db->query("delete from industry.sovmestnyy_uchet where id=".$this->uri->segment(3));
		redirect("billing/point/".$id);
	}
	function sovm_ab()
	{	
		$sql="SELECT * FROM industry.sovm_ab where values_set_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('sovmestnyy_absolutnyy_view',$data);
		$this->execute("add_sovm_ab");
	}
	function add_sovm_ab()
	{
		$sql='select id,dogovor||\'  \'||name as firm_info from industry.firm order by dogovor';
		$data['firms']=$this->db->query($sql);
		$data['values_set_id']=$this->uri->segment(3);
		$this->load->view('add_sovmestnyy_ab_view',$data);
	}
	function adding_sovm_ab()
	{
		$this->db->insert("industry.sovm_absolutnyy",$_POST);
		redirect("billing/values_sets/".$_POST['values_set_id']);
	}
	function delete_sovm_ab()
	{
		$query=$this->db->query("select * from industry.sovm_absolutnyy where id=".$this->uri->segment(3));
		$id=$query->row()->values_set_id;
		$this->db->query("delete from industry.sovm_absolutnyy where id=".$this->uri->segment(3));
		redirect("billing/values_sets/".$id);
	}
	function sovm_by_counter()
	{
		$sql="SELECT * FROM industry.sovm_by_counter where billing_point_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('sovm_by_counter_view',$data);
		$this->execute("add_sovm_by_counter");
	}
	function add_sovm_by_counter()
	{
		$sql='select id,dogovor||\'  \'||name as firm_info from industry.firm  order by dogovor';
		$data['firms']=$this->db->query($sql);
		$data['point_id']=$this->uri->segment(3);
		$this->load->view('add_sovm_by_counter_view',$data);
	}
	function adding_sovm_by_counter()
	{
		$this->db->insert("industry.sovm_by_counter_value",$_POST);
		redirect("billing/point/".$_POST['billing_point_id']);
	}
	function delete_sovm_by_counter()
	{
		$query=$this->db->query("select * from industry.sovm_by_counter_value where id=".$this->uri->segment(3));
		$id=$query->row()->billing_point_id;
		$this->db->query("delete from industry.sovm_by_counter_value where id=".$this->uri->segment(3));
		redirect("billing/point/".$id);
	}

	function delete_ot_nadbavka()
	{
		$id=$this->db->query("select billing_point_id from industry.nadbavka_otnositelnaya where id=".$this->uri->segment(3) )->row()->billing_point_id;
		$this->db->query("delete from industry.nadbavka_otnositelnaya where id=".$this->uri->segment(3));
		redirect("billing/point/".$id);
	}
	function delete_ab_nadbavka()
	{
		$id=$this->db->query("select values_set_id from industry.nadbavka_absolutnaya where id=".$this->uri->segment(3) )->row()->values_set_id;
		$this->db->query("delete from industry.nadbavka_absolutnaya where id=".$this->uri->segment(3));
		redirect("billing/values_sets/".$id);
	}

	function vedomost()
	{
		$this->load->library("pdf/pdf");
		
		$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 9);
        
        // add a page
        $this->pdf->AddPage('L');
		
		$sql="SELECT * FROM industry.firm WHERE id=".$_POST['firm_id'];
		$data['firm']=$this->db->query($sql)->row();
		$sql="SELECT * FROM industry.vedomost WHERE firm_id=".$_POST['firm_id']." and period_id=".$_POST['period_id'];
		$data['vedomost']=$this->db->query($sql);
		$sql="SELECT * FROM industry.vedomost_itog 		where firm_id=".$_POST['firm_id']." and period_id=".$_POST['period_id'];
		$data['itogo']=			$this->db->query($sql)->row();
		$sql="SELECT * FROM industry.firm_oplata_itog 	where firm_id=".$_POST['firm_id']." and period_id=".$_POST['period_id'];
		if ($this->db->query($sql)->num_rows()>0)
		{
			$data['oplata_value']=	$this->db->query($sql)->row()->oplata_value;
		}
		else 
			$data['oplata_value']=0;
		$string=$this->load->view("reports/vedomost",$data,TRUE);
		$this->pdf->writeHTML($string);
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I'); 
		
	}
	function pre_akt_sverki()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['r']= $this->	db->get('industry.firm')->row();
		$sql="SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
		$data['current_period']=$this->db->query($sql)->row()->current_period;
		$data['period']=$this->db->get('industry.period');
		$data['firm_id']=$this->uri->segment(3);
		$this->left();
		$this->load->view("pre_akt_sverki",$data);
		$this->load->view("right");
	}
	function akt_sverki()
	{
		$sql="select * from industry.vedomost where firm_id={$_POST['firm_id']} and  period_id between {$_POST['start_period_id']} and {$_POST['finish_period_id']}";
		$data['akt']=$this->db->query($sql);

		$this->load->view('reports/akt_sverki',$data);
	}
	function pre_report_to_nalogovaya()
	{
		$sql="SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
		$data['current_period']=$this->db->query($sql)->row()->current_period;
		$this->db->order_by("id");
		$data['period']=$this->db->get('industry.period');
		$this->left();
		$this->load->view("pre_report_to_nalgovaya",$data);
		$this->load->view("right");
	}
	function report_to_nalogovaya()
	{
		$sql="select * from industry.report_to_nalogovaya where period_id>={$_POST['start_period_id']} and  period_id <={$_POST['finish_period_id']}";
		$data['nalog']=$this->db->query($sql);
		$this->load->view('reports/report_to_nalogovaya',$data);
	}
	function pre_svod()
	{
		$data['period']=$this->db->get('industry.period');
		$this->left();
		$this->load->view("pre_svod",$data);
		$this->load->view("right");
	}
		function pre_svodik()
	{
		$data['period']=$this->db->get('industry.period');
		$this->left();
		$this->load->view("pre_svodik",$data);
		$this->load->view("right");
	}
	function svod()
	{
		$sql="";
		$params='firm.name as firm_name ';
		$php='echo "<tr><td>".$j++."</td><td>".$r->firm_name."</td>";';
		$period_head="<tr><td>№</td><td>Предприятие</td>";
		$period_sum_end="echo \"<tr><td colspan=2>ИТОГО</td>";
		$period_sum_head="";
		for ($j=$_POST['start_period_id'];$j<=$_POST['finish_period_id'];$j++)
		{
			$tablename="\"".$j."\"";
			$columnname="\"col_".$j."\"";
			$sql.=" left join industry.\"7-re\" as $tablename 
				on $tablename.period_id=$j and $tablename.firm_id=firm.id ";
			$params.=", coalesce($tablename.itogo_kvt,0) as $columnname ";
			$php.='echo "<td align=right>$r->col_'.$j.'</td>";$sum_'.$j.'+= $r->col_'.$j.';';
			$this->db->where('id',$j);
			$period_head.="<td>".$this->db->get('industry.period')->row()->name."</td>";
			$period_sum_end.="<td align=right >\".f_d(\$sum_$j).\"</td>";
			$period_sum_head.="\$sum_$j=0;";
			
		}
		$period_sum_end.="\";";
		$sql="select $params from industry.firm ".$sql." ".($_POST['org_type']>0?" where firm.org_type=".$_POST['org_type']." ":"")." order by firm.dogovor";
		$data['res']=$this->db->query($sql);
		$data['php']=$php;
		$data['sum_end']=$period_sum_end;
		$data['period_sum_head']=$period_sum_head;
		$data['period_head']=$period_head;
		$this->load->view("reports/svod",$data);
	}
	function report_prom()
	{
		$sql="select * from industry.prom";
		$data['prom']=$this->db->query($sql);
		$this->load->view('reports/prom',$data);
	}
	function report_budjet()
	{
		$sql="select * from industry.budjet";
		$data['prom']=$this->db->query($sql);
		$this->load->view('reports/budjet',$data);
	}
	function report_neprom()
	{
		$sql="select * from industry.neprom";
		$data['prom']=$this->db->query($sql);
		$this->load->view('reports/neprom',$data);
	}
	function svodik()
	{
		$sql="";
		$params='firm.name as firm_name ';
		$php='echo "<tr><td>".$j++."</td><td>".$r->firm_name."</td>";';
		$period_head="<tr><td>№</td><td>Предприятие</td>";
		$period_sum_end="echo \"<tr><td colspan=2>ИТОГО</td>";
		$period_sum_head="";
		for ($j=$_POST['start_period_id'];$j<=$_POST['finish_period_id'];$j++)
		{
			$tablename="\"".$j."\"";
			$columnname="\"col_".$j."\"";
			$sql.=" left join industry.\"7-re\" as $tablename 
				on $tablename.period_id=$j and $tablename.firm_id=firm.id ";
			$params.=", coalesce($tablename.oplata_value,0) as $columnname ";
			$php.='echo "<td align=right>$r->col_'.$j.'</td>";$sum_'.$j.'+= $r->col_'.$j.';';
			$this->db->where('id',$j);
			$period_head.="<td>".$this->db->get('industry.period')->row()->name."</td>";
			$period_sum_end.="<td align=right >\".f_d(\$sum_$j).\"</td>";
			$period_sum_head.="\$sum_$j=0;";
			
		}
		$period_sum_end.="\";";
		$sql="select $params from industry.firm ".$sql." ".($_POST['org_type']>0?" where firm.org_type=".$_POST['org_type']." ":"")." order by firm.dogovor";
		$data['res']=$this->db->query($sql);
		$data['php']=$php;
		$data['sum_end']=$period_sum_end;
		$data['period_sum_head']=$period_sum_head;
		$data['period_head']=$period_head;
		$this->load->view("reports/svodik",$data);
	}
	function pre_schetfactura()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['r']= $this->	db->get('industry.firm');
		$sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
		$data['period']=$this->db->query($sql);
		$this->left();
		$this->load->view("pre_schetfactura",$data);
		$this->load->view("right");
	}
	function pre_schetfactura2()
	{
		$data['firm_id']=$_POST['firm_id'];
		$data['period_id']=$_POST['period_id'];
		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$data['r']= $this->db->get('industry.schetfactura_date');	

		$data['org_info']=$this->db->get("industry.org_info")->row();
		$this->db->where("id",$_POST['firm_id']);
		$data['firm']=$this->db->get("industry.firm")->row();
		
        $this->db->where('period_id',$_POST['period_id']);
        $data['max_schet_number'] = $this->db->get("shell.max_schet_number")->row()->schet_number;

		$this->left();
		$this->load->view("pre_schetfactura2",$data);
		$this->load->view("right");
	}	
	function schetfactura()
	{
		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$this->db->update('industry.schetfactura_date',
			array(
				'edit1'=>$_POST['edit1'],
				'edit2'=>$_POST['edit2'],
				'edit3'=>$_POST['edit3'],
				'edit4'=>$_POST['edit4'],
				'edit5'=>$_POST['edit5'],
				'edit6'=>$_POST['edit6']
			)
		);
		
		#FINE
		$this->db->where('period_id', $_POST['period_id']);
		$this->db->where('firm_id', $_POST['firm_id']);
		$isset_fine = $this->db->get("industry.fine_source_data")->num_rows();
		if ((isset($isset_fine)) and ($isset_fine > 0)) {
			$this->db->where('period_id', $_POST['period_id']);
			$this->db->where('firm_id', $_POST['firm_id']);
			$data['fine_value'] = $this->db->get("industry.fine_source_data")->row()->fine_value;
		}
		#END FINE
		
		$this->load->plugin('chislo');
		$data['header']=!isset($_POST['is_akt'])?"Счет-фактура ":"Акт выполенных работ ";
		$sql="SELECT * FROM industry.org_info";
		$data['org']=$this->db->query($sql)->row();
		$sql="select * from industry.schetfactura where tariff_value<>0 and firm_id=".$_POST['firm_id'].' and period_id='.$_POST['period_id'];
		$data['s']=$this->db->query($sql)->result();
		$this->db->where('firm_id',$_POST['firm_id']);
		$this->db->where('period_id',$_POST['period_id']);
		$data['schetfactura_date']=$this->db->get('industry.schetfactura_date')->row();
		$this->db->where('id',$_POST['firm_id']);
		$data['firm']=$this->db->get('industry.firm')->row();
		$data['edit1']=$_POST['edit1'];
		$data['edit2']=$_POST['edit2'];
		$data['edit3']=$_POST['edit3'];
		$data['edit4']=$_POST['edit4'];
		$data['edit5']=$_POST['edit5'];
		$data['edit6']=$_POST['edit6'];
		$data['number_schet']=$_POST['number_schet'];
		$data['data_schet']=$_POST['data_schet'];
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get('industry.period')->row();
		$this->db->where('id',$data['firm']->bank_id);
		$data['bank']=$this->db->get('industry.bank')->row();
		
		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$data['itog']=$this->db->get("industry.vedomost_itog")->row();
		
		if (!isset($_POST['html']))
		{	
	if (isset($_POST['new_schetfactura1'])) {
			$string = $this->load->view("reports/schetfactura_new",$data,TRUE);


			$this->load->library("pdf/pdf");

			$this->pdf->SetSubject('TCPDF Tutorial');
			$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			$this->pdf->SetAutoPageBreak(TRUE);
			// set font
			$this->pdf->SetFont('dejavusans', '', 9);

			// add a page
			$this->pdf->AddPage('P');

			$this->pdf->writeHTML($string);

			//Close and output PDF document
			$this->pdf->Output('example_001.pdf', 'I'); }
			if (isset($_POST['nakladnaya']))
		$this->load->view("reports/nakladnaya",$data );
			
			if (isset($_POST['akt_vypolnenyh_rabot']))
		{
		$this->load->view("reports/avp",$data);
		
		}}  else
				
			{$string = $this->load->view("reports/schetfactura_new",$data,TRUE);


			$this->load->library("pdf/pdf");

			$this->pdf->SetSubject('TCPDF Tutorial');
			$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			$this->pdf->SetAutoPageBreak(TRUE);
			// set font
			$this->pdf->SetFont('dejavusans', '', 9);

			// add a page
			$this->pdf->AddPage('P');

			$this->pdf->writeHTML($string);

			//Close and output PDF document
			$this->pdf->Output('example_001.pdf', 'I'); 

			}
		
	}
	function pre_schetoplata()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['r']= $this->	db->get('industry.firm');
		$sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
		$data['period']=$this->db->query($sql);
		$this->left();
		$this->load->view("pre_schetoplata",$data);
		$this->load->view("right");
	}
	function pre_schetoplata2()
	{
		$data['firm_id']=$_POST['firm_id'];
		$data['period_id']=$_POST['period_id'];
		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$data['r']= $this->db->get('industry.schetfactura_date');		
		
		$sql="select distinct tariff_value from industry.schetfactura where  period_id=".$_POST['period_id'];
		$data['tariffs']=$this->db->query($sql);

		
		$this->left();
		$this->load->view("pre_schetoplata2",$data);
		$this->load->view("right");
	}
	function schetoplata()
	{
		$data['number_schet']=$_POST["number_schet"];
		$data['tovar_name']=$_POST["tovar_name"];
		$sql="SELECT * FROM industry.org_info";
		$data['org']=$this->db->query($sql)->row();
		$this->db->where('firm_id',$_POST['firm_id']);
		$this->db->where('period_id',$_POST['period_id']);
		$data['schetfactura_date']=$this->db->get('industry.schetfactura_date')->row();
		$this->load->plugin('chislo');
		$this->db->where('id',$_POST['firm_id']);
		$data['firm']=$this->db->get('industry.firm')->row();
		
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get('industry.period')->row();
		
		$this->db->where('id',$data['firm']->bank_id);
		$data['bank']=$this->db->get("industry.bank")->row();
		$data['schet']=!isset($_POST['schet'])?" НА ОПЛАТУ":"-ФАКТУРА";
		
		
		if ($_POST['type']=="by_tenge")
		{
			$tariff_value=$_POST['tariff_value'];
			$tariff_kvt=$_POST['tariff'];
			$buf;
			for($j=0;$j<$_POST['tariff_count'];$j++)
			{
				if ($tariff_value[$j]>0)
						$buf[$j]=$tariff_kvt[$j] / $tariff_value[$j]/((100+$data['period']->nds)/100);
					else 
					$buf[$j]=$tariff_kvt[$j];
			}
			$data['tariff_kvt']=$buf;
		}
		else 
			$data['tariff_kvt']=$_POST['tariff'];
		$data['tariff_value']=$_POST['tariff_value'];
		$data['tariff_count']=$_POST['tariff_count'];
		$data['data_schet']=$_POST['data_schet'];
		
		
		$this->load->view("reports/schetoplata",$data);
	}
	function akt()
	{
		$query="SELECT * FROM industry.current_akt WHERE values_set_id=".$this->uri->segment(3);
		$data['query']=$this->db->query($query);
		$this->load->view("akt_view",$data);
		$this->execute("add_akt");
	}
	function delete_akt()
	{
		$id=$this->db->query("select values_set_id from industry.akt where id=".$this->uri->segment(3) )->row()->values_set_id;
		$this->db->query("delete from industry.akt where id=".$this->uri->segment(3));
		redirect("billing/values_sets/".$id);
	}
	function add_akt()
	{
		$data['vs_id']=$this->uri->segment(3);
		$this->load->view("add_akt",$data);
	}
	function adding_akt()
	{
		
		$_POST['tariff_value']=4;
		$this->db->insert("industry.akt",$_POST);
		redirect("billing/values_sets/".$_POST['values_set_id']);
	}
	function edit_pokaz()
	{
		$this->left();
		$sql="SELECT * FROM industry.counter_add_pokaz where firm_id=".$this->uri->segment(3);
		$data['firm_id']=$this->uri->segment(3);
		$data['pokaz']=$this->db->query($sql);
		$this->load->view("edit_pokaz",$data);
		$this->load->view("right");
	}
	function delete_pokazanie2()
	{
		$query=$this->db->query("select * from industry.counter_value where id=".$this->uri->segment(3));
		$id=$query->row()->values_set_id;
		$query=$this->db->query("select distinct firm_id from industry.counter_add_pokaz where values_set_id=$id");
		$firm_id=$query->row()->firm_id;
		$this->db->query("delete from industry.counter_value where id=".$this->uri->segment(3));
		redirect("billing/edit_pokaz/$firm_id#$id");
	}
	function adding_pokazanie2()
	{
		$sql="SELECT firm_id from industry.firmid_by_values_set where values_set_id=".$_POST['values_set_id'];
		$w=$this->db->query($sql)->row()->firm_id;
		
		$this->session->set_userdata(array('day'=>$_POST['day'],'month'=>$_POST['month'],'year'=>$_POST['year']));
		$data=$_POST['year']."-".$_POST['month']."-".$_POST['day'];
		if (!checkdate($_POST['month'],$_POST['day'],$_POST['year'])) 
		{			
			redirect("billing/edit_pokaz/".$w);
		}		
		$_POST['data']=date("Y-m-d", mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
		unset($_POST['year']);
		unset($_POST['day']);
		unset($_POST['month']);	
		$this->db->insert("industry.counter_value",$_POST);
		
		redirect("billing/edit_pokaz/$w#".($this->uri->segment(3)+1));
	}
	//! Отчеты
	function reports()
	{
		$this->left();
		$this->load->view("reports");
		$this->load->view("right");
	}
	function vih_7_re_form()
	{
		$sql="SELECT * from industry.period order by id";
		$data['periods']=$this->db->query($sql);
		$this->left();
		$this->load->view("reports/form/vih_7_re_form",$data);
		$this->load->view("right");
	}
	function vih_7_re()
	{
		$data['org_info']=$this->db->get("industry.org_info")->row();
		$sql='select * from industry.period where id='.$_POST['period_id'];
		$data['period']=$this->db->query($sql)->row();
		$sql='select * from industry."7-re" where 
			period_id='.$_POST['period_id'];
		if ($_POST['type']==2)
			$sql.=" and 
			coalesce(\"7-re\".debet_value,0) - 
			coalesce(\"7-re\".kredit_value,0)	+
			coalesce(\"7-re\".nachisleno,0) -
			coalesce(\"7-re\".oplata_value,0)>0 ";
		if ($_POST['type']==3)
			$sql.=" and 
			coalesce(\"7-re\".debet_value,0) - 
			coalesce(\"7-re\".kredit_value,0)	+
			coalesce(\"7-re\".nachisleno,0) -
			coalesce(\"7-re\".oplata_value,0)<0 ";
		$data['sql_result']=$this->db->query($sql);		
		$this->load->view("reports/7-re",$data);
	}
		function vih_kvt_form()
	{
		$this->db->order_by('id');
		$data['period']=$this->db->get('industry.period');
		$this->left();
		$this->load->view("pre_kvt",$data);
		$this->load->view("right");
	}
	function vih_kvt()
	{

		$where="";
		$sql="";
		$params='firm.name as firm_name ';
		$php='echo "<tr><td>".$j++."</td><td>".$r->dogovor."</td><td align=left>".$r->firm_name."</td><td>".$r->subgroup."</td>";';
		$period_head="<tr><td>№</td><td>Дог.</td><td>Предприятие</td><td>Подгруппа</td>";
		for ($j=$_POST['start_period_id'];$j<=$_POST['finish_period_id'];$j++)
		{
			$tablename="\"".$j."\"";
			$columnname="\"col_".$j."\"";
			$sql.=" left join industry.\"vse_kvt\" as $tablename 
				on $tablename.period_id=$j and $tablename.firm_id=firm.id ";
			$params.=", coalesce($tablename.itogo_kvt,0) as $columnname ";
			$php.='echo "<td align=right>".dottozpt($r->col_'.$j.')."</td>";';
			$this->db->where('id',$j);
			$period_head.="<td>".$this->db->get('industry.period')->row()->name."</td>";
		}
		$sql="select $params,firm_subgroup.name as subgroup,firm.dogovor from industry.firm left join industry.firm_subgroup on firm.subgroup_id=firm_subgroup.id ".$sql." $where order by firm.subgroup_id";
		$data['res']=$this->db->query($sql);
		$data['php']=$php;
		$data['period_head']=$period_head;
		$this->load->view("reports/vse_kvt1",$data);
	}
	function vih_2_re()
	{
		
		$data['org_info']=$this->db->get("industry.org_info")->row();
		$sql='select * from industry.period where id='.$_POST['period_id'];
		$data['period']=$this->db->query($sql)->row();
		if ($_POST['ture_id']!=-1)
		{
			$this->db->where('id',$_POST['ture_id']);
			$data['ture']=$this->db->get("industry.ture")->row()->name;
			$this->db->where('period_id',$_POST['period_id']);
			$this->db->where('firm_ture_id',$_POST['ture_id']);
			$data['sql_result']=$this->db->get('industry."2-re_by_ture"');
		}
		else
		{
			$data['ture']=NULL;
			$this->db->where('period_id',$_POST['period_id']);
			$data['sql_result']=$this->db->get("industry.\"2-re\"");
		}		
		$this->load->view("reports/2-re",$data);
	}
	function vih_2_re_form()
	{
		$this->db->order_by("name");
		$data['ture']=$this->db->get("industry.ture");
		$sql="SELECT * from industry.period order by id";
		$data['periods']=$this->db->query($sql);
		$this->left();
		$this->load->view("reports/form/vih_2_re_form",$data);
		$this->load->view("right");
	}
	function vih_analiz_kredit_debit()
	{
		$sql='select * from industry.analiz_of_change_debet_kredit where period_id=4';
		$data['analiz']=$this->db->query($sql)->result();
		$this->load->view('reports/analiz_of_change_debet_kredit',$data);	
	}
	function rashod_electro ()
	{
		$sql="SELECT * FROM industry.firm where id=".$this->uri->segment(3);
		$data['firm_data']=$this->db->query($sql)->row();
		$sql="SELECT * FROM industry.rashod_electro where firm_id=".$this->uri->segment(3);
		$data['points_data']=$this->db->query($sql);
		$this->load->view('reports/rashod_electro',$data);
	}
	function counters_by_type()
	{
		$sql="select * from industry.counters_by_type where counter_data_finish is null order by counter_type_id,ture_id,dogovor";
		$data['counters']=$this->db->query($sql);
		$this->load->view('reports/counters_by_type',$data);		
	}
	function reported_firms_form()
	{
		$this->db->order_by("id");
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("reports/form/reported_firms_form",$data);
		$this->load->view("right");
	}
	function reported_firms()
	{
		$this->db->where('id',$_POST['ture_id']);
		$data["ture"]=$this->db->get('industry.ture')->row();
		$sql="select * from industry.reported_firms where firm_close_id is ".(!$_POST['reported_or_notreported']?" not ":"")." null and ture_id=".$_POST['ture_id']."  order by dogovor";
		$data['type']=$_POST['reported_or_notreported'];
		$data['firms']=$this->db->query($sql);
		$this->load->view('reports/reported_firms',$data);
	}
	function pre_debet_inc()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view("pre_debet_inc",$data);
		$this->load->view("right");
	}
	function debet_inc()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['sql_result']=$this->db->get("industry.debet_inc");
		$data['org_info']=$this->db->get("industry.org_info")->row();
		$this->load->view("reports/debet_inc",$data);
	}
	function pre_dolgi()
	{
		$this->db->order_by("id");
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("pre_dolgi",$data);
		$this->load->view("right");
	}
	function dolgi()
	{
		$data['we']=$this->db->get("industry.org_info")->row();
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->order_by("dogovor");
		$this->db->where("dolg::numeric(24,2)>",0);
		$this->db->where("firm_ture_id",$_POST['ture_id']);
		$data['firms']=$this->db->get('industry.dolgi');
		
		$this->db->where("id",$_POST['ture_id']);
		$data['ture']=$this->db->get('industry.ture')->row();
		
		$this->load->view('reports/dolgi',$data);
	}
	function pre_kredit()
	{
		$this->db->order_by("id");
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("pre_kredit",$data);
		$this->load->view("right");
	}
	function kredit()
	{
		$data['we']=$this->db->get("industry.org_info")->row();
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->order_by("dogovor");
		$this->db->where("dolg::numeric(24,2)<",0);
		$this->db->where("firm_ture_id",$_POST['ture_id']);
		$data['firms']=$this->db->get('industry.dolgi');
		
		$this->db->where("id",$_POST['ture_id']);
		$data['ture']=$this->db->get('industry.ture')->row();
		
		$this->load->view('reports/kredit',$data);
	}
	function pre_snyatie_counter_value()
	{
		$this->db->order_by("id");
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("reports/form/snyatie_counter_value",$data);
		$this->load->view("right");
	}
	function snyatie_counter_value()
	{
		$data['we']=$this->db->query("select * from industry.org_info")->row();
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->where('id',$_POST['ture_id']);
		$data['values']=$this->db->get("industry.snyatie_counter_value");
		$this->load->view('reports/snyatie_counter_value',$data);
	}
	function list_of_firms()
	{ 	
		$sql="select * from industry.list_of_firms";
		$data['firms']=$this->db->query($sql);
		
		$this->load->view('reports/list_of_firms',$data);
	}
	
	
	
	function user_list()
	{
		$data['users']=$this->db->get('industry.user');
		$this->load->view("user_list",$data);
	}
	function oborotka()
	{
		$this->db->where('firm_id',$this->uri->segment(3));
		$data['oborotka']=$this->db->get('industry.oborotka')->row();
		$this->left();
		$this->load->view("oborotka",$data);
		$this->load->view("right");
	}
	function firm_oplata()
	{
		$this->db->where('firm_id',$this->uri->segment(3));
		$data['firm_oplata']=$this->db->get('industry.firm_oplata')->result();
		$this->left();
		$this->load->view("firm_oplata",$data);
		$this->load->view("right");
	}
	function delete_counter()
	{
		$sql="select point_id from industry.counter where id=".$this->uri->segment(3);
		$point_id=$this->db->query($sql)->row()->point_id;
		$sql="select industry.delete_counter(".$this->uri->segment(3).") as is_deleted;";
		$is_deleted=$this->db->query($sql)->row()->is_deleted;
		$this->session->set_flashdata('is_deleted', $is_deleted);
		redirect("billing/point/".$point_id);		
	}

	function delete_billing_point()
	{
        $this->check_billing_point($this->uri->segment(3));
		$sql="select firm_id from industry.billing_point where id=".$this->uri->segment(3);
		$firm_id=$this->db->query($sql)->row()->firm_id;
		$sql="select count(*) as count from industry.counter where point_id=".$this->uri->segment(3);
		$count=$this->db->query($sql)->row()->count;
		if ($count==0)
		{
			$count=-1;
			$sql="delete from industry.billing_point where id=".$this->uri->segment(3);
			$this->db->query($sql);
		}
		
		$this->session->set_flashdata('is_deleted',$count);
		redirect("billing/firm/".$firm_id);		 
	}
	function edit_billing_point()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['point']=$this->db->get('industry.billing_point')->row();
		$this->db->order_by('name');
		$data['tp']=$this->db->get('industry.tp');
		$data['power_group']=$this->db->get('industry.firm_power_group');
		$data['point_id']=$this->uri->segment(3);
		$this->left();
		$this->load->view('edit_billing_point',$data);
		$this->load->view('right');
	}
	function edition_billing_point()
	{
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('industry.billing_point',$_POST);
		redirect("billing/edit_billing_point/".$this->uri->segment(3));
	}
	function edit_permission()
	{
		$sql="select * from industry.user where id=".$this->uri->segment(3);
		$data['perm']=$this->db->query($sql)->row_array();		
		$this->left();
		$this->load->view('edit_permission',$data);
		$this->load->view("right");
	}	
	function edition_permission()
	{
		$sql="select * from industry.user where id=".$_POST['id'];
		$perm=$this->db->query($sql)->row_array();		
		$this->db->where('id', $_POST['id']);		
		unset( $perm['id'] );
		unset( $perm['password'] );		
		foreach ($perm as $key => $_f) 
		{
			if (($key!='name') and ($key!='login') and ($key!='profa'))
			{
				if (isset($_POST[$key]))
				$f[$key]='t';
				else $f[$key]='f';
			}
		}
		$this->db->update('industry.user', $f);
		redirect('billing/edit_permission/'.$_POST['id']);
	}
	//! DBASE для работы с DBF
	function to_date($var)
	{
		$year=substr($var,0,4);
		$month=substr($var,4,2);
		$day=substr($var,6,2);
		return $day.".".$month.".".$year;
	}
	function dbase()
	{
		$this->db->query("delete from industry.oplata_buf");
		$period=$this->db->query("select * from industry.period where 
			id in 	(select value::integer from industry.sprav	where name='current_period')")->row();
		$sql="";
		set_time_limit(0);		
		$db = dbase_open("c:/oplata/OPLATA.dbf", 0);
		
		if ($db)
		{			
			for ($i=1;$i<dbase_numrecords($db)+1;$i++)
			{
				$rec=dbase_get_record_with_names($db,$i);
				
				$year=substr($rec['DATA'],0,4);
				$month=substr($rec['DATA'],4,2);
				$day=substr($rec['DATA'],6,2);
				
				$data=mktime(0,0,0,$month,$day,$year);
				$data=date("Y-m-d",$data);
				if (($data>= $period->begin_date)and($data<=$period->end_date))
				{
					$rec['DATA']=$this->to_date($rec['DATA']);
					$rec['DATA_V']=$this->to_date($rec['DATA_V']);
					
					if (strlen(trim($rec['VO']))==0) $rec['VO']=0;
					
					$sql.="\nINSERT INTO industry.oplata_buf(
					 data, un_nom, dog, data_v, n_dokum, sum, schet, vo) values 
					 ('{$rec['DATA']}',{$rec['UN_NOM']},{$rec['DOG']},
					 '{$rec['DATA_V']}',{$rec['N_DOKUM']},{$rec['SUM']},
					 '{$rec['SCHET']}',{$rec['VO']});\n";
				}				
			}
			dbase_close($db);
			
			$this->db->query($sql);
			
			$d["d"]=$this->db->get('industry.oplata_unknown_dogovor');
			$d["s"]=$this->db->get('industry.oplata_unknown_schet');
			$this->load->view("oplata/import",$d);
		}
		else 
			echo "База не открыта";		
	}
	function oplata_import()
	{		
		
		$this->db->query(
		"
		delete from industry.oplata where data between  
		   (select period.begin_date from industry.period
                 left join industry.sprav on sprav.name='current_period'
					where period.id=sprav.value) 
					 and (select period.end_date from industry.period
                 left join industry.sprav on sprav.name='current_period'
					where period.id=sprav.value) ;
		insert into industry.oplata 
			(firm_id,data,document_number,payment_number_id,value,nds)
			select industry.firm_id_by_dogovor(dog) as firm_id,data,n_dokum, industry.schet_id_by_name(schet),
			sum/(1+industry.current_nds()/100),industry.current_nds() from industry.oplata_buf 
			where industry.firm_id_by_dogovor(dog) is not null and industry.schet_id_by_name(schet) is not null AND oplata_buf.vo <> 4"
			);
		#по договору
		$this->db->query(
            "DELETE FROM industry.fine_oplata WHERE data BETWEEN
               (SELECT period.begin_date FROM industry.period
                     LEFT JOIN industry.sprav ON sprav.name='current_period'
                        WHERE period.id=sprav.value)
                         AND (SELECT period.end_date FROM industry.period
                     LEFT JOIN industry.sprav ON sprav.name='current_period'
                        WHERE period.id=sprav.value) ;
             INSERT INTO industry.fine_oplata
                (firm_id,data,document_number,payment_number_id,value,nds)
                SELECT 
                    industry.firm_id_by_dogovor(dog) AS firm_id,
					--industry.firm_id_by_nomer1c(nomer1c) AS firm_id,
                    data,
                    n_dokum, 
                    industry.schet_id_by_name(schet),
                    sum/(1+industry.current_nds()/100),
                    industry.current_nds() 
                FROM industry.oplata_buf
                WHERE 
				industry.firm_id_by_dogovor(dog) IS NOT NULL 
				--firm_id_by_nomer1c(nomer1c) IS NOT NULL 
                AND industry.schet_id_by_name(schet) IS NOT NULL
                AND oplata_buf.vo = 4"
        );
		#$this->db->query("select * from industry.load_oplata()");
		redirect ("billing");
	}
	function jpeg()
	{
		
	}
	function gd_info()
	{
		gd_info();
	}
	function com()
	{
		$xls = new COM("Excel.Application");
		$xls->Application->Visible = 1;
		$xls->Workbooks->Add();
		$range=$xls->Range("A1");
		$range->Value = "Проба записи";
		// Сохраняем документ
		$xls->Workbooks[1]->SaveAs( "c:/test.xls");
		$xls->Quit();
		$xls->Release();
		$xls = Null;
		$range = Null;
		echo "Hello";
	}

	function change_password()
	{
		$this->left();
		$this->load->view('sprav/change_password');
		$this->load->view('right');
	}
	function changing_password()
	{
		$sql="select * from industry.user where login='{$this->session->userdata('login')}' and password=md5('{$_POST['old_pass']}')";
		$count=$this->db->query($sql)->num_rows();
		if ($count>0)
		{
			if ($_POST['new_pass_1']==$_POST['new_pass_2'])
			{			
				$this->db->where('id',$this->session->userdata('id'));
				$this->db->update('industry.user',array('password'=>md5($_POST['new_pass_1'])));
				$this->session->set_flashdata('ischanged','yes');
			}
			else 
			$this->session->set_flashdata('ischanged','not_ident');
		} 
		else $this->session->set_flashdata('ischanged','old_pass_error');		
		redirect('billing/change_password');
	}
	// работа с оплатой
	
	function org_info()
	{
		$this->left();
		$data['org_info']=$this->db->get("industry.org_info")->row();
		$this->load->view('sprav/org_info',$data);
		$this->load->view('right');
	}
	function changing_org_info()
	{
		$this->db->update('industry.org_info',$_POST);
		$this->session->set_flashdata('is_changed','изменено');
		redirect("billing/org_info");
	}
	function pre_oplata_info()
	{
		$this->left();
		$this->load->view('pre_oplata_info');
		$this->load->view('right');
	}
	function oplata_info()
	{
		$sql="select * from industry.oplata_info where oplata_data between '{$_POST['begin_date']}' and '{$_POST['end_date']}';";
		$data['oplata_info']=$this->db->query($sql);
		$this->load->view('reports/oplata_info',$data);
	}
	
	
	function oplata()
	{
		if ($this->session->userdata('begin_data')=="")
		{
			$sql="select period.* from industry.period 
					left join industry.sprav on sprav.name='current_period'
						where period.id=sprav.value";
			$period=$this->db->query($sql)->row();
			$this->session->set_userdata(array('begin_data'=>$period->begin_date,'end_data'=>$period->end_date));
		}
		
		/*FINE*/
		$this->db->where('data >= ', $this->session->userdata('begin_data'));
		$this->db->where('data <= ', $this->session->userdata('end_data'));
		$data['fine_oplata'] = $this->db->get("industry.fine_oplata_edit")->result();
		/*END FINE*/	
		
		$sql="select * from industry.oplata_edit where data between '".$this->session->userdata('begin_data')."' and '".$this->session->userdata('end_data')."'";
		$data['oplata']=$this->db->query($sql);
		$this->load->view('oplata/index',$data);
	}
	function oplata_po_schetam()
	{
		if (!isset($_POST['start']))
				{
					$this->db->where('period_id',$_POST['period_id']);
					$data['oplata']=$this->db->get('industry.oplata_po_schetam');
				}
			else
				{
					$sql="select * from industry.oplata_po_schetam where data between '{$_POST['start']}' and 
								'{$_POST['end']}'";
					$data['oplata']=$this->db->query($sql);
				}
		
		$this->load->view("oplata/po_schetam",$data);
	}
	function pre_platejnye_dokumenty()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view("pre_platejnye_dokumenty",$data);
		$this->load->view("right");
	}
	function platejnye_dokumenty()
	{
		$this->db->where('period_id',$_POST['period_id']);
		$data['oplata']=$this->db->get('industry.platejnye_dokumenty');
		$this->load->view("oplata/platejnye_dokumenty",$data);
	}
	function oplata_svod()
	{
		$this->db->where('period_id',$_POST['period_id']);
		$data['oplata']=$this->db->get("industry.oplata_svod");
		$this->load->view('oplata/svod',$data);
	}
	function pre_oplata_svod()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view("pre_oplata_svod",$data);
		$this->load->view("right");
	}
	function pre_oplata_po_schetam()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view("pre_oplata_po_schetam",$data);
		$this->load->view("right");
	}
	function change_oplata_period()
	{
		$this->session->set_userdata($_POST);
		redirect('billing/oplata');
	}
	
	/*
	function adding_oplata()
	{

		$sql="select count(*) from industry.firm where dogovor=".$_POST['dogovor'];
		$count=$this->db->query($sql)->row()->count;
		
		$sql="select id,name from industry.firm where dogovor=".$_POST['dogovor'];
		$query=$this->db->query($sql);
		if ($count>0)
		{
			$firm_id=$this->db->query($sql)->row()->id;
			$firm_name=$this->db->query($sql)->row()->name;
			$data['firm_id']=$firm_id;
			$data['nds']=$_POST['nds'];
			$sql="select count(*)  from industry.payment_number where number='".$_POST['payment_number']."'";
			$count=$this->db->query($sql)->row()->count;
			$sql="select id from industry.payment_number where number='".$_POST['payment_number']."'";
			echo $sql;
			$query=$this->db->query($sql);
			if ($count>0)
			{
				$data['payment_number_id']=$query->row()->id;
				$this->session->set_userdata(
					array( 	'data'			=>	$_POST['data'],
						'number'	=>	$_POST['payment_number']
					        ) 
				);
				$data['value']=$_POST['value']/1.12;
				$data['data']=$_POST['data'];
				if (isset($_POST['vzaimoraschet']))
				{
					$data['vzaimoraschet']="true";
				}
				else 
					$data['vzaimoraschet']="false";
				$data['document_number']=$_POST['document_number'];
				$this->db->insert('industry.oplata',$data);
				$this->session->set_flashdata('added','true');
				$this->session->set_flashdata('firm_name',$firm_name);

			}
		}
		redirect('billing/oplata');
	}*/
	
	#метод переключения
	function adding_oplata()
    {

        $sql = "select count(*) from industry.firm where dogovor=" . $_POST['dogovor'];
        $count = $this->db->query($sql)->row()->count;
        $current_nds = $this->db->query("select * from industry.current_nds()")->row()->current_nds;
        $sql = "select id,name from industry.firm where dogovor=" . $_POST['dogovor'];
        $query = $this->db->query($sql);
        if ($count > 0) {
            $firm_id = $this->db->query($sql)->row()->id;
            $firm_name = $this->db->query($sql)->row()->name;
            $data['firm_id'] = $firm_id;
            $data['nds'] = $_POST['nds'];
            $sql = "select count(*)  from industry.payment_number where number='" . $_POST['payment_number'] . "'";
            $count = $this->db->query($sql)->row()->count;
            $sql = "select id from industry.payment_number where number='" . $_POST['payment_number'] . "'";
            echo $sql;
            $query = $this->db->query($sql);
            if ($count > 0) {
                $data['payment_number_id'] = $query->row()->id;
                $this->session->set_userdata(array(
                    'data' => $_POST['data'], 'number' => $_POST['payment_number']
                ));
                $data['value'] = $_POST['value'] / (($current_nds + 100) / 100);
                $data['data'] = $_POST['data'];
                $data['document_number'] = $_POST['document_number'];
                if ($_POST['pay_type'] == 1) {
				if (isset($_POST['vzaimoraschet']))
				{
					$data['vzaimoraschet']="true";
				} else 
				{
					$data['vzaimoraschet']="false";
				}
                    $this->db->insert('industry.oplata', $data);
                } else {
                    $this->db->insert('industry.fine_oplata', $data);
                }
                $this->session->set_flashdata('added', 'true');
                $this->session->set_flashdata('firm_name', $firm_name);

            }
        }
        redirect('billing/oplata');
    }
	
	function pre_svod_po_tp()
	{
		$data['ture']=$this->db->get("industry.ture");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_svod_po_tp',$data);
		$this->load->view('right');
	}
	function svod_po_tp()
	{
		
		$this->load->library("pdf/pdf");
		
		$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 7);
        
        // add a page
        $this->pdf->AddPage();
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get('industry.period');
		$this->db->where('ture_id',$_POST['ture_id']);
		$this->db->where('period_id',$_POST['period_id']);
		$data['ture']=$this->db->get('industry.svod_po_tp');
		$string=$this->load->view("reports/svod_po_tp",$data,TRUE);
		
        $this->pdf->writeHTML($string);
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I'); 
	}
	
	function pre_nadbavka_info()
	{
		$this->left();
		$this->db->order_by('id');
		$data['periods']=$this->db->get("industry.period");
		$data['users']=$this->db->get("industry.user");
		$this->load->view("pre_nadbavka_info",$data);
		$this->load->view("right");
	}
	function nadbavka_info()
	{
		$this->db->where("period_id",$_POST['period_id']);
		if ($_POST['user_id']!=-1)
		{
			$this->db->where("user_id",$_POST['user_id']);
		}
		$data['firms']=$this->db->get("industry.nadbavka_info");
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		if ($_POST['user_id']!=-1)
		{
			$this->db->where("id",$_POST['user_id']);
			$data['user']=$this->db->get("industry.user")->row()->name;
		}
		else
		{
			$data['user']='-1';
		}
		$this->load->view("reports/nadbavka_info",$data);
	}
	
	function pre_subabonent()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->db->order_by("id");
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("pre_subabonent",$data);
		$this->load->view("right");
	}
	function subabonent()
	{
		$this->db->where('period_id',$_POST['period_id']);
		if ($_POST['ture_id']!=-1)
			$this->db->where('ture_id',$_POST['ture_id']);
		$data['firms']=$this->db->get('industry.subabonent');
		
		$this->load->view('reports/subabonent',$data);
	}
	
		function pre_holostoy_hod()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_holostoy_hod',$data);
		$this->load->view('right');
	}
	
	function holostoy_hod()
	{
	$sql = "SELECT * FROM industry.holostoy_hod where period_id=".$_POST['period_id'];
		$data['hol']=$this->db->query($sql);
		$this->load->view("reports/holostoy_hod",$data);
	}
	
	function dispetcherskaya()
	{
	$sql = "SELECT * FROM industry.dispetcherskaya";
		$data['disp']=$this->db->query($sql);
		$this->load->view("dispetcherskaya",$data);
	}
	function billing_point_info_all()
	{
		$data['info']=$this->db->get("industry.billing_point_info_all");
		$this->load->view("reports/billing_point_info_all",$data);
	}
	
	function pre_ip_obshiy()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_ip_obshiy',$data);
		$this->load->view('right');
	}
	
	function ip_obshiy()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['ip']=$this->db->get("industry.ip_obshiy_tar");
		$this->load->view("reports/ip_obshiy",$data);
	}
	
	function doljniki_za_period_form()
	{
		$sql="SELECT * from industry.period order by id";
		$data['periods']=$this->db->query($sql);
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view("reports/form/doljniki_za_period",$data);
		$this->load->view("right");
	}
	
	function doljniki_za_period()
	{
		$data['org_info']=$this->db->get("industry.org_info")->row();
		$sql='select * from industry.period where id='.$_POST['period_id'];
		$data['period']=$this->db->query($sql)->row();
		$data['use_ture']='0';
		$sql='select * from industry.doljniki_za_period where 
			period_id='.$_POST['period_id'];
		if ($_POST['type']==2){
			$sql.=" and 
			coalesce(\"doljniki_za_period\".debet_value,0) - 
			coalesce(\"doljniki_za_period\".kredit_value,0)	+
			coalesce(\"doljniki_za_period\".nachisleno,0) -
			coalesce(\"doljniki_za_period\".oplata_value,0)>0 ";
			$data['nazv']=" дебиторов ";
			}
		if ($_POST['type']==3){
			$sql.=" and 
			coalesce(\"doljniki_za_period\".debet_value,0) - 
			coalesce(\"doljniki_za_period\".kredit_value,0)	+
			coalesce(\"doljniki_za_period\".nachisleno,0) -
			coalesce(\"doljniki_za_period\".oplata_value,0)<0 ";
			$data['nazv']=" кредиторов ";
			}
		if ($_POST['ture_id']!=-1){
			$data['use_ture']='1';
			$sql.=" and firm_ture_id= ".$_POST['ture_id'];
			$this->db->where("id",$_POST['ture_id']);
			$name=$this->db->get("industry.ture")->row()->name;
			$data['ture']=" <br>по ТУРЭ $name";
		}
		$data['sql_result']=$this->db->query($sql);		
		$this->load->view("reports/doljniki_za_period",$data);
	}
	
	function pre_akt_snyatiya_pokazaniy()
	{
		$this->db->order_by('id');
		$data['period']=$this->db->get('industry.period');
		$this->left();
		$this->load->view("pre_akt_snyatiya_pokazaniy",$data);
		$this->load->view("right");
	}
	
	
	function akt_snyatiya_pokazaniy()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get('industry.period');
		$this->db->where("period_id",$_POST['period_id']);
		$this->db->order_by('dogovor');
		$data['vedomost']=$this->db->get('industry.akt_snyatiya_pokazaniy');
		$this->load->view("reports/akt_snyatiya_pokazaniy",$data);
		}
	
	function statisticheskiy_otchet_new()
	{
		$data['otchet']=$this->db->get("industry.stat_otchet_new");
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->load->view("reports/stat_otchet",$data);
	}
	
	function pre_ne_potrebil()
	{
		$this->left();
		$this->db->order_by('id');
		$data['periods']=$this->db->get("industry.period");
		$data['users']=$this->db->get("industry.user");
		$this->load->view("pre_ne_potrebil",$data);
		$this->load->view("right");
	}
	function ne_potrebil()
	{
		$this->db->where("period_id",$_POST['period_id']);
		if ($_POST['user_id']!=-1)
		{
			$this->db->where("user_id",$_POST['user_id']);
		}
		$data['firms']=$this->db->get("industry.ne_potrebil");
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		if ($_POST['user_id']!=-1)
		{
			$this->db->where("id",$_POST['user_id']);
			$data['user']=$this->db->get("industry.user")->row()->name;
		}
		else
		{
			$data['user']='-1';
		}
		$this->load->view("reports/ne_potrebil",$data);
	}
	
	function pre_svod_saldo_po_ture()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_svod_saldo_po_ture',$data);
		$this->load->view('right');
	}
	function svod_saldo_po_ture()
	{
		$this->load->library("pdf/pdf");
		
		
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->where('period_id',$_POST['period_id']);
		$data['ture']=$this->db->get('industry.svod_saldo_po_ture');
		$string=$this->load->view("reports/svod_saldo_po_ture",$data,TRUE);
		
		$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        
        // set font
        $this->pdf->SetFont('dejavusans', '', 10);
        
        // add a page
        $this->pdf->AddPage();
        $this->pdf->writeHTML($string);
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I'); 
	}
	function pre_energo_24()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_energo_24',$data);
		$this->load->view('right');
	}
	function energo_24()
	{
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->where('period_id',$_POST['period_id']);
		$data['energo']=$this->db->get('industry.energo_24');
		$this->load->view('reports/energo_24',$data);
	}
	function naryad_zadanie_po_ture()
	{
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->where('ture_id',$_POST['ture_id']);
		//$this->db->where('phase',$_POST['phase']);
		
		$data['naryad']=$this->db->get('industry.naryad_zadanie_po_ture');
		$this->load->view('reports/naryad_zadanie_po_ture',$data);
	}
	function pre_naryad_zadanie_po_ture()
	{
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view('pre_naryad_zadanie_po_ture',$data);
		$this->load->view('right');
	}
	function pre_oborotka_with_predoplata()
	{
		$data['ture']=$this->db->get("industry.ture");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_oborotka_with_predoplata',$data);
		$this->load->view('right');
	}
	function oborotka_with_predoplata()
	{
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get('industry.period')->row();
		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_ture_id',$_POST['ture_id']);
		
		$data['oborotka']=$this->db->get("industry.oborotka_with_predoplata");
		$this->load->view('reports/oborotka_with_predoplata',$data);
	}
	function pre_svod_oplat_po_firmam()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_svod_oplat_po_firmam',$data);
		$this->load->view('right');
	}
	function svod_oplat_po_firmam()
	{
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$this->db->order_by('dogovor');
		$this->db->where('period_id',$_POST['period_id']);
		$data['svod']=$this->db->get("industry.svod_oplat_po_firmam");
		$this->load->view('reports/svod_oplat_po_firmam',$data);
	}
	function pre_poleznyy_otpusk()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_poleznyy_otpusk',$data);
		$this->load->view('right');
	}
	function poleznyy_otpusk()
	{
		$data['org_info']=$this->db->get('industry.org_info')->row();
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->db->where('period_id',$_POST['period_id']);
		$data['otpusk']=$this->db->get('industry.poleznyy_otpusk');
		$this->load->view('reports/poleznyy_otpusk',$data);
	}
	function copy_user()
	{
		$this->db->where('id','7');
		$user=$this->db->get("industry.user")->row_array();
		unset($user['id']);unset($user['name']);
		$this->db->insert('industry.user',$user);
	}

    function pre_perehod()
    {
        $last_ua = $this->db->get("industry.last_user_actions")->result();
        $last_user_actions = array();
        foreach ($last_ua as $lua) {
            $last_user_actions[$lua->class_method] = $lua->max_action_time;
        }
        $data['last_user_actions'] = $last_user_actions;
        $this->left();
        $this->load->view('pre_perehod', $data);
        $this->load->view('right');
    }

    function perehod()
	{
		$this->db->query("select industry.goto_next_period_fine();");
		$array = array(1 => 'Переход в следующий месяц прошел успешно!');
		$this->session->set_flashdata('success', $array);		
		redirect("billing");
	}
	function oplata_delete()
	{
		$sql="delete from industry.oplata where id=".$this->uri->segment(3);
		$this->db->query($sql);
		redirect('billing/oplata');
	}
	function statisticheskiy_otchet()
	{
		$data['otchet']=$this->db->get("industry.statisticheskiy_otchet");
		$data['period_name']=$this->db->query("select industry.current_period() as current_period")->row();
		$this->load->view("reports/statisticheskiy_otchet",$data);
	}
	function billing_point_info()
	{
		$this->db->where('id',$this->uri->segment(3));
		$data['firm']=$this->db->get("industry.firm")->row();
		$this->db->where('firm_id',$this->uri->segment(3));
		$data['info']=$this->db->get("industry.billing_point_info");
		$this->load->view("reports/billing_point_info",$data);
	}
	function pre_oborotno_svodnaya_vedomost()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_oborotno_svodnaya_vedomost',$data);
		$this->load->view('right');
	}
	function oborotno_svodnaya_vedomost()
	{
		$this->db->where('id',$_POST['period_id']);
		$data['period_name']=$this->db->get('industry.period')->row()->name;
		$this->db->where("period_id",$_POST['period_id']);
		$data['oborotka']=$this->db->get("industry.oborotno_svodnaya_vedomost");
		$this->load->view("reports/oborotno_svodnaya_vedomost",$data);	
	}
	function pre_diff_tariff_controll()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_diff_tariff_controll',$data);
		$this->load->view('right');
	}
	function diff_tariff_controll()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.diff_tariff_controll");
		$this->load->view("reports/diff_tariff_controll",$data);
	}
	function pre_diff_tariff_spisok()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_diff_tariff_spisok',$data);
		$this->load->view('right');
	}
	function diff_tariff_spisok()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.diff_tariff_spisok");
		$this->load->view("reports/diff_tariff_spisok",$data);
	}
	function pre_diff_tariff_controll_3()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_diff_tariff_controll_3',$data);
		$this->load->view('right');
	}
	function diff_tariff_controll_3()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.diff_tariff_controll_3");
		$this->load->view("reports/diff_tariff_controll_3",$data);
	}
	function pre_diff_tariff_spisok_3()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_diff_tariff_spisok_3',$data);
		$this->load->view('right');
	}
	function diff_tariff_spisok_3()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.diff_tariff_spisok_3");
		$this->load->view("reports/diff_tariff_spisok_3",$data);
	}
	function nachislenie_v_buhgalteriu()
	{
		$nach = $this->db->query("select *from industry.nachislenie_v_buhgalteriu union all select * from industry.nach_fine");
		
		set_time_limit(0);
		$db = dbase_open("c:/oplata/nach1.dbf", 2);
		
		if ($db)
		{			
			for ($i=1;$i<dbase_numrecords($db)+1;$i++)
			{
				dbase_delete_record($db, $i);	
			}
			dbase_pack($db);
			dbase_close($db);
			
			$db2 = dbase_open("c:/oplata/nach1.dbf", 2);
			foreach ($nach->result() as $n)
			{
				dbase_add_record($db2,
					array (
						$this->d2($n->DATA),
						$n->NAIM,
						$n->DOG,$n->NACH,$n->NACH_SUM,$n->NACH_NDS, $n->KVT, $this->dob($n->NOMERSCHET), $this->d2($n->KONDATA),$n->NPENI
					));
			}
			
			dbase_close($db2);			
		}
		else 
			echo "База не открыта";		
	}
	function pre_nachislenie_v_analiz()
	{
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view("pre_nachislenie_v_analiz",$data);
		$this->load->view("right");
	}
	function pre_graph()
	{
		$sql="SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
		$data['current_period']=$this->db->query($sql)->row()->current_period;
		
		$this->db->order_by("id");
		$data['period']=$this->db->get("industry.period");
		$data['firm_id']=$this->uri->segment(3);
		$this->left();		
		$this->load->view('pre_graph',$data);
		$this->load->view('right');
	}
	function closer_number($max)
	{
		$p=1;
		$pow=1;
		$pow2=100;
		while ($p<$max)
		{
			$pow=$pow*10;
			$pow2=$pow*10;
			$p=$pow;
			while (($pow2>$p) and ($max>$p))
			{
				$p+=$pow;
			}
		}
		$p+=$pow;
		$numbers="0";
		for ($j=1;$j<11;$j++)
		{
			$numbers.= ", ".$this->f_d_graph($p*$j/10);
		}
		return $numbers;
	}
	function graph()
	{
		$periods="";
		$i="";
		$fi=$_POST["start_period_id"];
		$ei=$_POST["finish_period_id"];
		$first=1;
		$sql="select * from industry.graph where firm_id ={$this->uri->segment(3)} and period_id between $fi and $ei";
		
		$this->db->where("id",$this->uri->segment(3));
		$data['firm_info']=$this->db->get("industry.firm")->row();
		$res=$this->db->query($sql);
		$max=0;
		foreach ($res->result() as $p)
		{
			if ($max<$p->itogo_kvt)
			{
				$max=$p->itogo_kvt;
			}
			if ($first==1)
			{
				$periods.="'".$this->shortdate($p->period_begin_date)."'";
				$i.=$this->f_d_graph($p->itogo_kvt);
				$first=2;
			}
			else
			{
				$periods.=", '".$this->shortdate($p->period_begin_date)."' ";
				$i.=', '.$this->f_d_graph($p->itogo_kvt);
			}
		}
		
		
		$data['periods']=$periods;
		$data['itogo_kvt']=$i;
		$data['numbers']=$this->closer_number($max);
		$this->load->view('reports/graph',$data);
	}
	function ktp_vih()
	{
	$sql="SELECT * from industry.tp_firm";
	$data['firm']=$this->db->query($sql);
	$this->load->view("reports/ktp",$data);
	}
		function ktp_vih1()
	
	{
		$this->db->order_by('id');
		$data['periods']=$this->db->get('industry.period');
		$this->db->order_by('name');
		$data['tp']=$this->db->get('industry.tp');
		$this->left();
		$this->load->view("pre_kvtik",$data);
		$this->load->view("right");
	}

    function isRussian($text)
    {
        return preg_match('/[А-Яа-яЁё]/u', $text);
    }

    function perenos_rek1()
    {
        $db = dbase_open("c:/oplata/rekv.dbf", 2);
        if ($db) {
            header('Content-Type: text/html; charset="utf-8"');
            $this->db->order_by("dog");
            $nach = $this->db->get("industry.perenos_rekvizit");
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $russian_letters = array("А", "О", "Е", "С", "Х", "Р", "Т", "Н", "К", "В", "М");
            $english_letters = array("A", "O", "E", "C", "X", "P", "T", "H", "K", "B", "M");
            $incorrected_bins = array();
            $ei_mfo = array();
            $i = 0;
            $e = 0;

            $db2 = dbase_open("c:/oplata/rekv.dbf", 2);
            foreach ($nach->result() as $n) {

                //находим некорректные БИКи и МФО банков
                if ((mb_strlen(trim($n->mfo), 'UTF-8') != 8) and ($n->mfo != '0000000000')) {
                    $ei_mfo[$n->dog]['len'] = mb_strlen(trim($n->mfo), 'UTF-8');
                    $ei_mfo[$n->dog]['mfo'] = trim($n->mfo);
                    $ei_mfo[$n->dog]['bank'] = trim($n->bank);
                    $e++;
                }

                //обнуляем пустые МФО
                if (($n->mfo == '0000000000')) {
                    $n->mfo = '';
                }

                //находим некорректные БИНы организаций
                if ((mb_strlen(trim($n->bin), 'UTF-8') != 12) and (mb_strlen(trim($n->bin), 'UTF-8') != 0)) {
                    $incorrected_bins[$i]['dog'] = $n->dog;
                    $incorrected_bins[$i++]['bin'] = $n->bin;
                    $e++;
                }

                //обнуляем пустые БИНы
                if ((mb_strlen(trim($n->bin), 'UTF-8') == 0)) {
                    $n->bin = '';
                }

                //заменяем кириллицу на латиницу в МФО
                $n->mfo = str_replace($russian_letters, $english_letters, $n->mfo);

                //вдруг пропущен символ
                if ($this->isRussian($n->mfo)) {
                    echo "{$n->mfo} contains russian letters<br>";
                    $e++;
                }

                //пропуск цикла если есть ошибки
                if ($e != 0) {
                    $e = 0;
                    continue;
                }

                dbase_add_record($db2,
                    array(
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->name)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->dog)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->rnn)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->direct)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adres)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->schet)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->tel)), 'cp866', 'utf-8'),
                        $this->d2($n->data),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->sub)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bank)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->mfo)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->korr)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adresbank)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->otrasl)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bin)), 'cp866', 'utf-8'),
                        "0" . $n->dog1
                    )
                );
            }
            dbase_close($db2);

            echo "<b>Договора с некорректными БИНами:</b><br>";
            foreach ($incorrected_bins as $ib) {
                echo $ib['dog'] . ": " . $ib['bin'] . "<br>";
            }
            echo "<br>";
            echo "<b>Банки с некорректными МФО:</b><br>";
            foreach ($ei_mfo as $key => $ib) {
                echo $key . ": " . $ib['bank'] . ": " . $ib['mfo'] . ":" . $ib['len']."<br>";
            }
        } else {
            echo "DBF file is busy!";
        }
    }


    function perenos_nach()
    {
        set_time_limit(0);
        @$db = dbase_open("c:/oplata/schet.dbf", 2);
        if ($db) {
            $this->db->where('period_id', $this->get_cpi());
            $nach = $this->db->get("industry.schetfactura_to_1c");
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);
            $db2 = dbase_open("c:/oplata/schet.dbf", 2);
            foreach ($nach->result() as $n) {
                if ($n->dog1 == 0){
                    $array_error[] = "У договора #{$n->dog} некорректный номер 1C: {$n->dog1}";
                    continue;
                }

                if ($n->beznds == 0) {
                    $array_error[] = "Счет-фактуры, выписанная договору #{$n->dog}, нулевая";
                    continue;
                }

                if (strlen(trim($n->nomer)) == 0){
                    $array_error[] = "Номер счет-фактуры, выписанной договору #{$n->dog}, некорректный: {$n->nomer}";
                    continue;
                }

                dbase_add_record($db2,
                    array(
                        $n->dog,
                        $n->kvt,
                        $n->tarif, $n->beznds, $n->nds, $n->snds, $n->nomer, $this->d2($n->data), "0" . $n->dog1
                    )
                );
            }
            dbase_close($db2);
            $array = array(1 => 'Перенос прошел успешно!');
            $this->session->set_flashdata('success', $array);
            redirect('billing/pre_perehod');
        } else {
            $array = array(1 => 'Перенос не возможен. Закройте файл schet.dbf!');
            $this->session->set_flashdata('error', $array);
            redirect('billing/pre_perehod');
        }
    }


    function ktpwki()
	{
		$sql='select * from industry.period where id='.$_POST['period_id'];
		$data['period']=$this->db->query($sql)->row();
		
	
		$sql='select * from industry.hodor where tp_id='.$_POST['tp_id'];
		$data['t']=$this->db->query($sql)->row();
			$sql='select * from industry.hodor where 
			tp_id='.$_POST['tp_id'].'and period_id='.$_POST['period_id'];
		$data['sql_result']=$this->db->query($sql);
		$this->load->view("reports/ktpwki",$data);
	}
	function pre_analiz_mnogourovneviy_spisok()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_analiz_mnogourovneviy_spisok',$data);
		$this->load->view('right');
	}
	
	function analiz_mnogourovneviy_spisok()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.analiz_mnogourovneviy_spisok");
		$this->load->view("reports/analiz_mnogourovneviy_spisok",$data);
	}
	
	function pre_analiz_mnogourovneviy()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_analiz_mnogourovneviy',$data);
		$this->load->view('right');
	}
	
	function analiz_mnogourovneviy()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.analiz_mnogourovneviy");
		$this->load->view("reports/analiz_mnogourovneviy",$data);
	}
function pre_analiz_diff_tarif()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_analiz_diff_tarif',$data);
		$this->load->view('right');
	}
	
	function analiz_diff_tarif()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.analiz_diff_tarif");
		$this->load->view("reports/analiz_diff_tarif",$data);
	}
	
	function pre_analiz_diff_tarif_spisok()
	{
		$data['period']=$this->db->get("industry.period");
		$this->left();
		$this->load->view('pre_analiz_diff_tarif_spisok',$data);
		$this->load->view('right');
	}
	
	
	function analiz_diff_tarif_spisok()
	{
		$this->db->where("id",$_POST['period_id']);
		$data['period']=$this->db->get("industry.period")->row();
		$data['org']=$this->db->get("industry.org_info")->row();
		$this->db->where("period_id",$_POST['period_id']);
		$data['diff']=$this->db->get("industry.analiz_diff_tarif_spisok");
		$this->load->view("reports/analiz_diff_tarif_spisok",$data);
	}

	function nachislenie_v_analiz()
	{
		$this->db->where("period_id",$_POST['period_id']);
		$nach=$this->db->get("industry.analiz_po_tp");
		
		set_time_limit(0);
		$db = dbase_open("c:/oplata/anal_tp.dbf", 2);
		
		if ($db)
		{			
			for ($i=1;$i<dbase_numrecords($db)+1;$i++)
			{
				dbase_delete_record($db, $i);	
			}
			dbase_pack($db);
			dbase_close($db);
			
			$db2 = dbase_open("c:/oplata/anal_tp.dbf", 2);
			foreach ($nach->result() as $n)
			{
				dbase_add_record($db2,
					array (
						$n->ture_id,
						iconv("utf-8","windows-1251",$n->tp_name),
						$n->kvt,0,0
						)
					);
			}
			
			dbase_close($db2);			
		}
		else 
			echo "База не открыта";
		redirect ("billing");
	}
		function graph_test()
	{
		
		$this->load->library("pdf/pdf");
		
		$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 7);
        
        // add a page
        $this->pdf->AddPage();
		
		$data['1']=23;
		$string=$this->load->view("reports/graph_test",$data,TRUE);
		
        $this->pdf->writeHTML($string);
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I'); 
	}
	function pre_multi_tariff_count()
	{
		$data['ture']=$this->db->get("industry.ture");
		$this->left();
		$this->load->view('pre_multi_tariff_count',$data);
		$this->load->view('right');
	}
	function multi_tariff_count()
	{
		$this->db->where('id',$_POST['ture_id']);
		$data['ture']=$this->db->get('industry.ture')->row();
		$this->db->where('ture_id',$_POST['ture_id']);
		$data['firms']=$this->db->get("industry.multi_tariff_count");
		$this->load->view('reports/multi_tariff_count',$data);
	}
	
    function tariff_list()
    {
        $this->db->where("period_id = industry.current_period_id()");
        $data['tariff_list'] = $this->db->get('industry.tariff_current_value')->result();
        $this->left();
        $this->load->view('tariff_list', $data);
        $this->load->view('right');
    }

    function adding_tariff()
    {
        $this->db->insert("industry.tariff", $_POST);
        redirect("billing/tariff_list");
    }

    function tariff()
    {
        $tariff_id = $this->uri->segment(3);
        $this->db->where("tariff_id", $tariff_id);
        $this->db->order_by("data");
        $this->db->order_by("kvt");
        $data['tariff_info'] = $this->db->get("industry.tariff_list")->result();

        $this->db->where("id", $tariff_id);
        $data['tariff'] = $this->db->get("industry.tariff")->row();

        $this->left();
        $this->load->view('tariff_view', $data);
        $this->load->view('right');
    }

    public function adding_tariff_value()
    {
        $tariff_id = $this->uri->segment(3);
        $value = $_POST['value'];
        $kvt = $_POST['kvt'];
        $data = $_POST['data'];
        if ($kvt == 0) {
            die("kvt ne dolzhny byt' men'she nulya");
        }

        $this->db->where("tariff_id", $tariff_id);
        $this->db->where("period_id = industry.current_period_id()");
        $prev_values = $this->db->get("industry.tariff_current_value")->row();

        if ($data < $prev_values->tariff_data) {
            die("date error");
        }

        $this->db->insert("industry.tariff_period", array(
            'tariff_id' => $tariff_id,
            'data' => $data
        ));

        $tariff_period_id = $this->db->insert_id();

        $this->db->insert("industry.tariff_value", array(
            'tariff_period_id' => $tariff_period_id,
            'kvt' => $kvt,
            'value' => $value
        ));


        redirect("billing/tariff/{$tariff_id}");
    }

    public function delete_tariff_value()
    {
        $tariff_value_id = $this->uri->segment(3);

        $this->db->where("id", $tariff_value_id);
        $tariff_period_id = $this->db->get("industry.tariff_value")->row()->tariff_period_id;

        $this->db->where("id", $tariff_period_id);
        $tariff_id = $this->db->get("industry.tariff_period")->row()->tariff_id;

        $this->db->where("id", $tariff_value_id);
        $this->db->delete("industry.tariff_value");

        $this->db->where("tariff_period_id", $tariff_period_id);
        $tariff_period_num = $this->db->get("industry.tariff_value")->num_rows;

        if ($tariff_period_num == 0) {
            $this->db->where("id", $tariff_period_id);
            $this->db->delete("industry.tariff_period");
        }

        redirect("billing/tariff/{$tariff_id}");
    }

    public function delete_tariff()
    {
        $tariff_id = $this->uri->segment(3);
        $this->db->where("tariff_id", $tariff_id);
        $tariff_period_nums = $this->db->get("industry.tariff_period")->num_rows;
        if ($tariff_period_nums != 0) {
            exit("Can't drop this tariff. It has values!");
        }

        $this->db->where("id", $tariff_id);
        $this->db->delete("industry.tariff");
        redirect("billing/tariff_list");
    }
	
	function firmsrnn()
	{ 	
		$sql="select * from industry.firm_overview";
		$data['firms']=$this->db->query($sql);
		
		$this->load->view('reports/firmsrnn',$data);
	}
	
	#added
    function nach_by_period()
    {
        $period_id = $_POST['period_id'];
        $this->db->where('period_id', $period_id);
        $nach = $this->db->get("industry.nachislenie_v_buhgalteriu_period");

        set_time_limit(0);
        $db = dbase_open("c:/oplata/nach1.dbf", 2);

        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $db2 = dbase_open("c:/oplata/nach1.dbf", 2);
            foreach ($nach->result() as $n) {
                dbase_add_record($db2,
                    array(
                        $this->d2($n->DATA),
                        $n->NAIM,
                        $n->DOG, $n->NACH, $n->NACH_SUM, $n->NACH_NDS, $n->KVT, $n->NOMERSCHET, $this->d2($n->KONDATA)
                    ));
            }

            dbase_close($db2);
            echo "Nach uploaded!";
        } else {
            echo "База не открыта";
        }
    }
    #end of added

	
	/*ADD PERIODS*/
    function add_periods()
    {
        $months = array(
            1 => array('name' => 'Январь', 'end_day' => 31),
            2 => array('name' => 'Февраль', 'end_day' => 28),
            3 => array('name' => 'Март', 'end_day' => 31),
            4 => array('name' => 'Апрель', 'end_day' => 30),
            5 => array('name' => 'Май', 'end_day' => 31),
            6 => array('name' => 'Июнь', 'end_day' => 30),
            7 => array('name' => 'Июль', 'end_day' => 31),
            8 => array('name' => 'Август', 'end_day' => 31),
            9 => array('name' => 'Сентябрь', 'end_day' => 30),
            10 => array('name' => 'Октябрь', 'end_day' => 31),
            11 => array('name' => 'Ноябрь', 'end_day' => 30),
            12 => array('name' => 'Декабрь', 'end_day' => 31)
        );

        $last_period_info = $this->db->query("SELECT id, begin_date, end_date, name FROM industry.period WHERE id IN (SELECT max(id) FROM industry.period)")->row();
        $last_period_id = (int)$last_period_info->id;
        $last_period_date = explode('-', $last_period_info->begin_date);
        $start_day = '01';
        $end_day = 0;
        $start_month = 1;
        $end_month = 12;
        $new_months = array();
        if ($last_period_date[1] < 12) {
            $start_month = (string)($last_period_date[1] + 1);
            $start_year = (int)$last_period_date[0];
        } else {
            $start_year = $last_period_date[0] + 1;
        }
        var_dump($start_year);
        var_dump($start_month);
        $j = 0;
        for ($i = $start_month; $i <= $end_month; $i++) {
            if (($i == 2) and ($start_year%4 == 0)) {
                $end_day = $months[$i]['end_day']+1;
            } else {
                $end_day = $months[$i]['end_day'];
            }
            $new_months[$j]['id'] = ++$last_period_id;
            $new_months[$j]['begin_date'] =$start_year."-".sprintf("%02d", $i)."-".$start_day;
            $new_months[$j]['end_date'] = $start_year."-".sprintf("%02d", $i)."-".$end_day;
            $new_months[$j]['name'] = $months[$i]['name']." ".$start_year." г.";
            $new_months[$j]['nds'] = 12.0000;
            $j++;
        }
        for ($j = 0; $j<count($new_months); $j++){
            $data = array(
                'id' => $new_months[$j]['id'],
                'begin_date' => $new_months[$j]['begin_date'],
                'end_date' => $new_months[$j]['end_date'],
                'name' => $new_months[$j]['name'],
                'nds' => $new_months[$j]['nds']
            );
            $this->db->insert("industry.period", $data);
        }
		echo "added";
        #redirect("billing/period");
    }
   /*END ADD PERIODS*/
   	/*FINE*/
    /*начальная страница по пене*/
    public function fine_info()
    {
        if (isset($_POST['add_ref_rate'])) {
            $rate_data = $_POST['rate_data'];
            $rate_value = (float)$_POST['rate_value'];
            if ((strlen($rate_data) == 10) and ($rate_value > 0) and ($rate_value < 100)) {
                if ($this->db->insert('industry.ref_rate', array('data' => $rate_data, 'value' => $rate_value))) {
                    echo "Значение добавлено!";
                }
            } else {
                echo "Неверные значения!";
            }
        }

        if (isset($_POST['add_ref_coef'])) {
            $coef_data = $_POST['coef_data'];
            $coef_value = (float)$_POST['coef_value'];
            if ((strlen($coef_data) == 10) and ($coef_value > 0) and ($coef_value < 100)) {
                if ($this->db->insert('industry.ref_coef', array('data' => $coef_data, 'value' => $coef_value))) {
                    echo "Значение добавлено!";
                }
            } else {
                echo "Неверные значения!";
            }
        }

        $data['periods'] = $this->db->query(
            "select distinct(period.id) as id,
                period.name
             from industry.fine_saldo
             join industry.period on period.id = fine_saldo.period_id and fine_saldo.period_id-1< industry.current_period_id()
             order by period.id desc"
        )->result();

        $this->db->order_by('data');
        $data['ref_rates'] = $this->db->get('industry.ref_rate')->result();

        $this->db->order_by('data');
        $data['ref_coefs'] = $this->db->get('industry.ref_coef')->result();

        $this->left();
        $this->load->view('fine/fine_info', $data);
        $this->load->view('right');
    }

    public function pre_fine_akt_sverki()
    {
        $data['firm_id'] = (int)$this->uri->segment(3);
        $data['period'] = $this->db->query(
            "select * from industry.period
            where period.id <= industry.current_period_id()
            and extract(year from period.end_date) = industry.get_current_year()
            order by period.id desc"
        )->result();
        $this->left();
        $this->load->view('fine/pre_fine_akt_sverki', $data);
        $this->load->view('right');
    }

    public function fine_akt_sverki()
    {
        if ($_POST['period_id_start'] > $_POST['period_id_end']) {
            exit("Стартовая дата превышает конечную!");
        }

        $firm_id = $this->uri->segment(3);

        $this->db->where('id', $firm_id);
        $data['firm'] = $this->db->get("industry.firm")->row();

        $this->db->where('id', $_POST['period_id_end']);
        $data['period_end'] = $this->db->get("industry.period")->row();

        $this->db->where('start_period_id', $_POST['period_id_start']);
        $this->db->where('end_period_id', $_POST['period_id_end']);
        $this->db->where('firm_id', $firm_id);
        $isset_akt = $this->db->get("industry.fine_akt_sverki")->num_rows();
        $data['data_akta'] = date('d.m.Y');

        $ins_arr = array(
            'start_period_id' => $_POST['period_id_start'],
            'end_period_id' => $_POST['period_id_end'],
            'firm_id' => $firm_id,
            'data' => date('Y-m-d')
        );

        $this->db->where('start_period_id', $_POST['period_id_start']);
        $this->db->where('end_period_id', $_POST['period_id_end']);
        $this->db->where('firm_id', $firm_id);
        $isset_akt = $this->db->get('industry.fine_akt_sverki')->num_rows();

        if ($isset_akt == 0) {
            $this->db->insert('industry.fine_akt_sverki', $ins_arr);
            $data['akt_number'] = $this->db->insert_id();
        } else {
            $this->db->where('start_period_id', $_POST['period_id_start']);
            $this->db->where('end_period_id', $_POST['period_id_end']);
            $this->db->where('firm_id', $firm_id);
            $data['akt_number'] = $this->db->get("industry.fine_akt_sverki")->row()->id;

            $this->db->where('start_period_id', $_POST['period_id_start']);
            $this->db->where('end_period_id', $_POST['period_id_end']);
            $this->db->where('firm_id', $firm_id);
            $this->db->update('industry.fine_akt_sverki', array('data' => $data['data_akta']));
        }


        $data['org'] = $this->db->get("industry.org_info")->row();

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id >=', $_POST['period_id_start']);
        $this->db->where('period_id <=', $_POST['period_id_end']);
        $data['akt'] = $this->db->get("industry.fine_akt_sverki_source")->result();
        $data['firm_id'] = $firm_id;


        $this->load->view('fine/fine_akt_sverki', $data);
    }

    public function current_year_calendar()
    {
        $data['calendar'] = $this->db->get('industry.current_year_calendar')->result();
        $this->left();
        $this->load->view("fine/current_year_calendar", $data);
        $this->load->view('right');
    }

    public function change_calendar_day()
    {
        $day_id = $this->uri->segment(3);
        $this->db->where('day_id', $day_id);
        $is_off = $this->db->get('industry.fine_calendar')->row()->is_off;
        $is_off = $is_off == 0 ? 1 : 0;

        $this->db->where('day_id', $day_id);
        $this->db->update('industry.fine_calendar', array('is_off' => $is_off));

        redirect("billing/current_year_calendar");
    }

    /*подробное начисление пени для организации*/
    public function fine()
    {
        $url_firm_id = $this->uri->segment(3);
        $period_id = $this->db->query('select * from industry.current_period_id()')->row()->current_period_id;
        $data['fine_periods'] = $this->db->query(
            "select period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.firm_id = $url_firm_id and fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['fine_firm_oplata_periods'] = $this->db->query(
            "select distinct(period.id),
            period.name
            from industry.period
            join industry.fine_firm_oplata on fine_firm_oplata.period_id = period.id and fine_firm_oplata.firm_id = $url_firm_id
            group by period.name, period.id"
        )->result();

        $this->db->where('firm_id', $url_firm_id);
        $this->db->where('period_id', $period_id);
        $data['fine_saldo'] = $this->db->get("industry.fine_saldo")->row();

        $period_id = $this->db->query("select * from industry.current_period_id()")->row()->current_period_id;
        $this->db->where('period_id', $period_id);
        $this->db->where('firm_id', (int)$url_firm_id);
        $data['fine_firm'] = $this->db->get('industry.fine_firm')->row();
        $data['current_period'] = $period_id;
        $this->db->where('id', (int)$url_firm_id);
        $data['firm_info'] = $this->db->get('industry.firm')->row();

        $this->left();
        $this->load->view("fine/pre_firm_fine", $data);
        $this->load->view("right");
    }

    public function fine_all_firm_options()
    {
        $data['options'] = $this->db->query(
            "select
            firm_group.\"name\" as group_name,
            firm_subgroup.\"name\" as subgroup_name,
            firm.dogovor,
            firm.name as firm_name,
            fine_firm.*
            from industry.firm
            join industry.firm_subgroup on firm_subgroup.id = firm.subgroup_id
            join industry.firm_group on firm_group.id = firm_subgroup.group_id
            join industry.fine_firm on fine_firm.firm_id = firm.id and fine_firm.period_id = industry.current_period_id()
            order by 
            firm_group.\"name\",
            firm_subgroup.\"name\",
            firm.dogovor"
        )->result();

        $this->left();
        $this->load->view('fine/fine_all_firm_options', $data);
        $this->load->view("right");
    }

    public function change_fine_parameter()
    {
        if (isset($_POST['change_is_fine'])) {
            unset($_POST['change_is_fine']);

            if (isset($_POST['is_fine'])) {
                $_POST['is_fine'] = 'true';
            } else {
                $_POST['is_fine'] = 'false';
            }

            $period_id = $_POST['period_id'];
            $firm_id = $_POST['firm_id'];
            $this->db->where('period_id', $period_id);
            $this->db->where('firm_id', $firm_id);
            $is_fine_added = $this->db->get('industry.fine_firm')->num_rows();

            if ($is_fine_added == 0) {

            } else {
                $this->db->where('period_id', $period_id);
                $this->db->where('firm_id', $firm_id);
                $this->db->update('industry.fine_firm', array(
                    'is_fine' => $_POST['is_fine'],
                    'border_day' => $_POST['border_day'],
                    'is_calendar' => $_POST['is_calendar']
                ));
            }
            redirect('billing/fine/' . $firm_id);
        }
    }

    /*ведомость пени по фирме*/
    public function fine_firm()
    {
        $firm_id = (int)$this->uri->segment(3);

        $url_period = $this->uri->segment(4);
        if ($url_period) {
            $period_id = (int)$url_period;
        } else {
            $period_id = (int)$_POST['period_id'];
        }
        $this->db->where('id', $period_id);
        $data['period_info'] = $this->db->get('industry.period')->row();

        $this->db->where('id', $period_id - 1);
        $data['prev_period_info'] = $this->db->get('industry.period')->row();

        $data['current_ref_rate'] = $this->get_current_ref_rate($period_id);
        $data['other_ref_rate'] = $this->get_other_ref_rate($period_id);

        $data['current_ref_coef'] = $this->get_current_ref_coef($period_id);
        $data['other_ref_coef'] = $this->get_other_ref_coef($period_id);

        /*извлекаем исходные данные по организации*/
        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $fine_firm_info = $this->db->get("industry.fine_firm")->row();
        $data['border_day'] = $fine_firm_info->border_day;

        if ($fine_firm_info->is_calendar == 0) {
            $data['border_day'] = $this->db->query("select * from industry.get_working_day({$period_id},{$data['border_day']})")->row()->get_working_day;
        }

        $pre_month_days = $this->db->query(
            "SELECT 
               fine_calendar.\"day\" as calendar_day,
               fine_calendar.is_off,
               weekday.*
              FROM industry.fine_calendar
              JOIN industry.period 
                ON EXTRACT(MONTH FROM period.begin_date) = fine_calendar.\"month\"
                AND EXTRACT(YEAR FROM period.begin_date) = fine_calendar.\"year\"
              JOIN industry.weekday ON fine_calendar.day_of_week = weekday.day_number
              WHERE period.id = $period_id
              ORDER BY day_id"
        )->result();

        $data['month_days'] = array();
        foreach ($pre_month_days as $pre_month_day) {
            $data['month_days'][$pre_month_day->calendar_day]['is_off'] = $pre_month_day->is_off;
            $data['month_days'][$pre_month_day->calendar_day]['day_shortname'] = $pre_month_day->day_shortname;
        }

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $firm_veds = $this->db->get("industry.fine_source_data")->row();

        $data['fine_saldo'] = $this->db->query(
            "select * from industry.fine_saldo where period_id = $period_id and firm_id = $firm_id"
        )->row();

        if (!empty($firm_veds)) {
            $data['firm_veds'] = $firm_veds;
            $firm_veds->oplata = $this->get_oplata_fine($firm_id, $period_id);
            $this->load->view("fine/fine_firm", $data);
        } else {
            var_dump($firm_veds);
            echo "Ошибка. Обратитесь к программисту";
            session_start();
        }
    }

    /* ведомость пени по всем незакрытым должникам*/
    public function fine_all_firms()
    {
        $period_id = (int)$_POST['period_id'];
        $this->db->where('id', $period_id);
        $data['period'] = $this->db->get('industry.period')->row();
        $data['fine_arr'] = $this->fine_calc_firms($period_id);
        $this->load->view("fine/fine_all_firms", $data);
    }

    /*оплаты пени по организации*/
    public function fine_firm_oplata()
    {
        $firm_id = (int)$this->uri->segment(3);

        $this->db->where('id', $firm_id);
        $data['firm_info'] = $this->db->get("industry.firm")->row();

        $url_period = $this->uri->segment(4);
        if ($url_period) {
            $period_id = (int)$url_period;
        } else {
            $period_id = (int)$_POST['period_id'];
        }

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $data['fine_firm_oplata'] = $this->db->get('industry.fine_firm_oplata')->result();

        $this->db->where('id', $period_id);
        $data['period_info'] = $this->db->get("industry.period")->row();

        $this->left();
        $this->load->view("fine/fine_firm_oplata", $data);
        $this->load->view("right");
    }

    /*сальдо пени*/
    public function fine_saldo_origin()
    {
        $url_firm_id = $this->uri->segment(3);
        if ($url_firm_id) {
            $firm_id = (int)$url_firm_id;
        } else {
            $firm_id = (int)$_POST['period_id'];
        }

        $this->db->where('id', $firm_id);
        $data['firm_info'] = $this->db->get("industry.firm")->row();

        $this->db->where('firm_id', $firm_id);
        $data['fine_saldo_origin'] = $this->db->get('industry.fine_saldo_origin')->result();

        $this->left();
        $this->load->view("fine/fine_saldo_origin", $data);
        $this->load->view("right");
    }

    /*считает пеню для всех организаций*/
    private function fine_calc_firms($period_id)
    {
        $this->db->where('period_id', $period_id);
        $this->db->order_by('fine_value', 'DESC');
        $firms = $this->db->get("industry.fine_total")->result();

        $fine_arr = array();
        foreach ($firms as $firm) {
            if ($firm->fine_value > 0) {
                $fine_arr[$firm->firm_id]['name'] = $firm->name;
                $fine_arr[$firm->firm_id]['dogovor'] = $firm->dogovor;
                $fine_arr[$firm->firm_id]['saldo'] = round($firm->saldo_value, 2);
                $fine_arr[$firm->firm_id]['nach'] = round($firm->nach, 2);
                $fine_arr[$firm->firm_id]['fs_start'] = $firm->fs_start_value;
                $fine_arr[$firm->firm_id]['fs_end'] = $firm->fs_end_value;
                $fine_arr[$firm->firm_id]['fo'] = $firm->fo_value;
                $fine_arr[$firm->firm_id]['fine'] = $firm->fine_value;
            }

        }
        return $fine_arr;
    }

    /*получение действующей ставки рефинансирования на начало периода*/
    private function get_current_ref_rate($period_id)
    {
        return $this->db->query(
            "select ref_rate.value from industry.ref_rate, industry.period 
             where ref_rate.data<=period.begin_date and period.id = $period_id
             order by ref_rate.data desc limit 1"
        )->row()->value;
    }

    /*получение ставок рефинансирования, начавших свое действие в течение периода*/
    private function get_other_ref_rate($period_id)
    {
        $other_ref_rate = $this->db->query(
            "select EXTRACT(DAY FROM ref_rate.data)::INTEGER as day, ref_rate.value 
             from industry.ref_rate, industry.period 
             where ref_rate.data>=period.begin_date and ref_rate.data<=period.end_date 
             and period.id = $period_id
             order by ref_rate.data desc"
        )->result();
        $rate_buf = array();
        foreach ($other_ref_rate as $rate) {
            $rate_buf[(int)($rate->day)] = (float)($rate->value);
        }
        return $rate_buf;
    }

    /*получение действующего коэффициента для расчета пени на начало периода*/
    private function get_current_ref_coef($period_id)
    {
        return $this->db->query(
            "select ref_coef.value from industry.ref_coef, industry.period 
             where ref_coef.data<=period.begin_date and period.id = $period_id
             order by ref_coef.data desc limit 1"
        )->row()->value;
    }

    /*получение коэффициентов для расчета пени, начавших свое действие в течение периода*/
    private function get_other_ref_coef($period_id)
    {
        $other_ref_coef = $this->db->query(
            "select EXTRACT(DAY FROM ref_coef.data)::INTEGER as day, ref_coef.value 
             from industry.ref_coef, industry.period 
             where ref_coef.data>=period.begin_date and ref_coef.data<=period.end_date 
             and period.id = $period_id
             order by ref_coef.data desc"
        )->result();
        $coef_buf = array();
        foreach ($other_ref_coef as $coef) {
            $coef_buf[(int)($coef->day)] = (float)($coef->value);
        }
        return $coef_buf;
    }

    /*оплаты организации по дням*/
    private function get_oplata_fine($firm_id, $period_id)
    {
        /*извлекаем последний день месяца*/
        $this->db->where('id', $period_id);
        $end_date = $this->db->get('industry.period')->row()->end_date;
        $end_day = get_day_number($end_date);
        /*извлекаем все оплаты организации*/
        $oplata_list_ar = $this->db->query(
            "SELECT oplata.data as data,
             SUM((oplata.value * (100::numeric + oplata.nds))/100)::NUMERIC as value
             FROM industry.oplata, industry.period
             WHERE firm_id=$firm_id
             and oplata.data >=period.begin_date
             and oplata.data <=period.end_date
             and period.id = $period_id
             GROUP BY oplata.data"
        )->result();
        /*заносим оплаты в ассоц. массив*/
        $oplata_arr = array();

        for ($i = 0; $i <= $end_day; $i++) {
            $oplata_arr[$i] = 0;
        }

        foreach ($oplata_list_ar as $oplata_list) {
            if (($oplata_list->data !== NULL) and ($oplata_list->value !== NULL)) {
                $buf = explode('-', $oplata_list->data);
                $buf = $buf[2];
                $oplata_arr[(int)$buf] = (float)$oplata_list->value;
            }
        }
        ksort($oplata_arr);
        return $oplata_arr;
    }

	function fine_oplata_delete()
    {
        $sql = "DELETE FROM industry.fine_oplata WHERE id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect('billing/oplata');
    }
	
    public function pre_fine_7_re()
    {
        $data['periods'] = $this->db->query(
            "select 
                distinct(period.id),
                period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['ture'] = $this->db->get("industry.ture")->result();

        $this->left();
        $this->load->view('fine/pre_fine_7_re', $data);
        $this->load->view('right');
    }

    public function fine_7_re()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period_name'] = $this->db->get("industry.period")->row()->name;

        if ($_POST['ture_id'] == '-1') {
            $this->db->where('period_id', $_POST['period_id']);
        } else {
            $this->db->where('id', $_POST['ture_id']);
            $data['ture_name'] = $this->db->get("industry.ture")->row()->name;

            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('ture_id', $_POST['ture_id']);
        }
        $data['re'] = $this->db->get("industry.fine_7_re")->result();

        $this->load->view('fine/fine_7_re', $data);
    }

    public function pre_fine_2_re()
    {
        $data['periods'] = $this->db->query(
            "select 
                distinct(period.id),
                period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['ture'] = $this->db->get("industry.ture")->result();
        $this->left();
        $this->load->view('fine/pre_fine_2_re', $data);
        $this->load->view('right');
    }

    public function fine_2_re()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period_name'] = $this->db->get("industry.period")->row()->name;

        if ($_POST['ture_id'] == '-1') {
            $this->db->where('period_id', $_POST['period_id']);
            $data['re'] = $this->db->get("industry.fine_2_re")->result();
        } else {
            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('ture_id', $_POST['ture_id']);
            $data['re'] = $this->db->get("industry.fine_2_re_ture")->result();
            $this->db->where('id', $_POST['ture_id']);
            $data['ture_name'] = $this->db->get("industry.ture")->row()->name;
        }

        $this->load->view('fine/fine_2_re', $data);
    }

   public function pre_report_2000()
    {
        $data['period_years'] = $this->db->get("industry.period_years")->result();
        $this->left();
        $this->load->view("other_reports/pre_report_2000", $data);
        $this->load->view("right");
    }

    public function report_2000()
    {

        $period_year = $_POST['period_year'];
        $this->db->where('period_year', $period_year);
        $data['report'] = $this->db->get("industry.report_2000")->result();
        $this->load->view("other_reports/report_2000", $data);
    }
	
	function load_dbf_oplata()
    {
        $this->db->query("delete from industry.oplata_buf");
        $period = $this->db->query("select * from industry.period where 
			id in 	(select value::integer from industry.sprav	where name='current_period')")->row();
        $sql = "";
        set_time_limit(0);
        $db = dbase_open("c:/oplata/OPLATA.dbf", 0);

        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                $rec = dbase_get_record_with_names($db, $i);

                $year = substr($rec['DATA'], 0, 4);
                $month = substr($rec['DATA'], 4, 2);
                $day = substr($rec['DATA'], 6, 2);

                $data = mktime(0, 0, 0, $month, $day, $year);
                $data = date("Y-m-d", $data);
//                if (($data >= $period->begin_date) and ($data <= $period->end_date)) {
                    $rec['DATA'] = $this->to_date($rec['DATA']);
                    $rec['DATA_V'] = $this->to_date($rec['DATA_V']);

                    if (strlen(trim($rec['VO'])) == 0) $rec['VO'] = 0;

                    $sql .= "\nINSERT INTO industry.oplata_buf(
					 data, un_nom, dog, data_v, n_dokum, sum, schet, vo) values 
					 ('{$rec['DATA']}',{$rec['UN_NOM']},{$rec['DOG']},
					 '{$rec['DATA_V']}',{$rec['N_DOKUM']},{$rec['SUM']},
					 '{$rec['SCHET']}',{$rec['VO']});\n";
//                }
            }
            dbase_close($db);

            $this->db->query($sql);

            $d["d"] = $this->db->get('industry.oplata_unknown_dogovor');
            $d["s"] = $this->db->get('industry.oplata_unknown_schet');
            $this->load->view("oplata/import", $d);
        } else
            echo "База не открыта";
    }	
	
		public function export_rekvizit_schet()
    {
        /*проверка наличия дублирующихся номеров СФ*/
        $this->db->where('period_id', $this->get_cpi());
        $dup_schet_number = $this->db->get("shell.dup_schet_number");
        if ($dup_schet_number->num_rows>0) {
            $array_error = array(1 => 'Имеются повторяющиеся номера счетов-фактур!');
            foreach ($dup_schet_number->result() as $d){
                $array_error[] = "№{$d->dogovor}: номер счета-фактуры: {$d->schet_number}";
            }
            $this->session->set_flashdata('error', $array_error);
            redirect('billing/pre_perehod');
        }

        $db = dbase_open("c:/oplata/rschet.dbf", 2);
        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $this->db->where('period_id', $this->get_cpi());
            $nach = $this->db->get("shell.export_rekvizit_schet");

            $russian_letters = array("А", "О", "Е", "С", "Х", "Р", "Т", "Н", "К", "В", "М");
            $english_letters = array("A", "O", "E", "C", "X", "P", "T", "H", "K", "B", "M");
            $array_error = array();

            $db = dbase_open("c:/oplata/rschet.dbf", 2);
            foreach ($nach->result() as $n) {
                //проверка номера 1С
                if ($n->dog1 == 0){
                    $array_error[] = "№{$n->dog}: некорректный номер 1C - {$n->dog1}";
                    continue;
                }

                //находим некорректные БИКи и МФО банков
                if ((mb_strlen(trim($n->mfo), 'UTF-8') != 8) and ($n->mfo != '0000000000')) {
                    $array_error[] = "№{$n->dog}: неверный БИК банка - {$n->mfo}";
                    continue;
                }

                //обнуляем пустые МФО
                if (($n->mfo == '0000000000')) {
                    $n->mfo = '';
                }

                //находим некорректные БИНы организаций
                if (mb_strlen(trim($n->bin), 'UTF-8') != 12) {
                    $array_error[] = "№{$n->dog}: неверный БИН - {$n->bin}";
                    continue;
                }

                //обнуляем пустые БИНы
                if ((mb_strlen(trim($n->bin), 'UTF-8') == 0)) {
                    $n->bin = '';
                }

                //заменяем кириллицу на латиницу в МФО
                $n->mfo = str_replace($russian_letters, $english_letters, $n->mfo);

                //вдруг пропущен символ
                if ($this->isRussian($n->mfo)) {
                    $array_error[] = "№{$n->dog}: БИК банка содержит кириллицу - {$n->mfo}";
                    continue;
                }

                //проверка расчетного счета
                if(($n->mfo <> '') && (mb_strlen($n->schet, 'UTF-8') <> 20)){
                    $array_error[] = "№{$n->dog}: некорректный расчетный счет - {$n->schet}";
                    continue;
                }
				
                if(($n->mfo == '') && (mb_strlen($n->schet, 'UTF-8') <> 20)){
                    $n->schet = '';
                }	

                //проверка начисления
                if ($n->beznds == 0) {
                    $array_error[] = "№{$n->dog}: нулевая счет-фактура";
                    continue;
                }

                //проверка номера СФ
                if (strlen(trim($n->nomer)) == 0){
                    $array_error[] = "№{$n->dog}: некорректный номер счет-фактуры - {$n->nomer}";
                    continue;
                }

                dbase_add_record($db,
                    array(
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->name)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->dog)), 'cp866', 'utf-8'),
                        $this->d2($n->dog_data),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bin)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->mfo)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->schet)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adres)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->direct)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->sub)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->kvt)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->tarif)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->beznds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->nds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->snds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->nomer)), 'cp866', 'utf-8'),
                        $this->d2($n->sf_data),
                        $n->dog1
                    )
                );
            }
            dbase_close($db);

            $this->db->where("id", $this->get_cpi());
            $current_period = $this->db->get("industry.period")->row();
            $current_period = explode("-", $current_period->end_date);
            $month = $current_period[1];
            
            if (!copy("c:/oplata/rschet.dbf", "c:/oplata/zrnd{$month}.dbf")) {
                $array_error[] = "Не удалось скопировать файл rschet.dbf";
            }
            
            $array_success[] = 'Перенос прошел успешно!';
            $this->session->set_flashdata('success', $array_success);
            $this->session->set_flashdata('error', $array_error);
            redirect('billing/pre_perehod');
        } else {
            echo "DBF file is busy!";
        }
    }
	
}

?>