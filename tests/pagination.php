<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../config/database.php';
require __DIR__.'/../includes/functions.php';
require __DIR__.'/../includes/pagination.php';
$settings=['host'=>'127.0.0.1','port'=>3307,'user'=>'root','password'=>'','name'=>'pagination_'.bin2hex(random_bytes(6)).'_test'];
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$admin=new mysqli('127.0.0.1','root','','',3307);
$sql=explode('-- Realistic',file_get_contents(__DIR__.'/../database.sql'))[0];
$sql=str_replace('bug_management',$settings['name'],$sql);
$admin->multi_query($sql);
do { if($r=$admin->store_result())$r->free(); } while($admin->more_results()&&$admin->next_result());
function verify_page($condition,$label){if(!$condition)throw new RuntimeException($label);echo "PASS $label\n";}
try {
 for($i=1;$i<=250;$i++) query("INSERT INTO bugs(title,description,priority) VALUES (?, ?, ?)",'sss',["Pagination issue $i",'Disposable pagination fixture',$i%2?'High':'Low']);
 foreach([10,25,50,100] as $size){
  $seen=[];
  for($page=1;$page<=ceil(250/$size);$page++){
   $_GET=['page'=>$page,'per_page'=>$size];
   $data=paginated_rows("bugs WHERE status IN ('Open','In Progress')",'id ASC');
   verify_page(count($data)===min($size,250-($page-1)*$size)&&$pagination['total']===250,"250 records: size $size page $page");
   foreach($data as $r){if(isset($seen[$r['id']]))throw new RuntimeException('Duplicate row');$seen[$r['id']]=true;}
  }
  verify_page(count($seen)===250,"Every record reached once at size $size");
 }
 $_GET=['page'=>-5,'per_page'=>11];paginated_rows('bugs','id ASC');verify_page($pagination['size']===10&&$pagination['page']===1,'Invalid parameters fall back safely');
 $_GET=['page'=>99999,'per_page'=>100];paginated_rows('bugs','id ASC');verify_page($pagination['page']===3,'Out-of-range page clamped');
 $_GET=['page'=>2,'per_page'=>25,'priority'=>'High','sort'=>'title','search'=>'save & reload'];
 $data=paginated_rows('bugs WHERE priority = ?','id DESC','s',['High']);
 verify_page($pagination['total']===125&&count($data)===25&&$data[0]['id']>$data[24]['id'],'Filtered COUNT and LIMIT share predicate and sorting');
 ob_start();pagination_controls();$html=ob_get_clean();verify_page(strpos($html,'priority=High')!==false&&strpos($html,'search=save+%26+reload')!==false&&strpos($html,'name="page" value="1"')!==false,'Filters preserved and page-size form resets to page one');
 $data=paginated_rows('bugs WHERE id = ?','id','i',[999999]);verify_page(!$data&&$pagination['total']===0,'Empty results');
 ob_start();pagination_controls();$html=ob_get_clean();verify_page(strpos($html,'Showing 0–0 of 0')!==false&&substr_count($html,'aria-disabled="true"')===2,'Empty navigation disables both directions');
 query("INSERT INTO bug_history(bug_id,previous_status,new_status) SELECT id,'Open','In Progress' FROM bugs");
 $_GET=[];$data=paginated_rows('bug_history h JOIN bugs b ON b.id=h.bug_id','h.id DESC','',[],'h.*, b.title');verify_page(count($data)===10&&$pagination['total']===250,'History join paginated');
 query("UPDATE bugs SET status='Resolved' WHERE id<=125");$data=paginated_rows("bugs WHERE status='Resolved'",'id DESC');verify_page(count($data)===10&&$pagination['total']===125,'Resolved list paginated');
 for($i=251;$i<=1000;$i++)query("INSERT INTO bugs(title,description) VALUES (?,?)",'ss',["Pagination issue $i",'Disposable fixture']);
 $_GET=['page'=>100,'per_page'=>10];$data=paginated_rows('bugs','id ASC');verify_page(count($data)===10&&$data[0]['id']===991&&$pagination['pages']===100,'1000 records: final page retrieves correct range');
 echo "All pagination checks passed.\n";
} finally {$admin->query('DROP DATABASE `'.$settings['name'].'`');}
