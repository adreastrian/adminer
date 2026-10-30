<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.2.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.2.0";error_reporting(24575);set_error_handler(function($_d,$Dd){return!!preg_match('~^Undefined (array key|offset|index)~',$Dd);},E_WARNING|E_NOTICE);$he=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($he||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$Bn=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Bn)$$W=$Bn;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($h=null){return($h?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$kc=adminer()->credentials();$H=Driver::connect($kc[0],$kc[1],$kc[2]);return(is_object($H)?$H:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$xg=substr($t,-1);return
str_replace($xg.$xg,$xg,substr($t,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($Ga,$w,$l=null){return($Ga&&array_key_exists($w,$Ga)?$Ga[$w]:$l);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($T){return
in_array($T,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$n){$T=$n["type"];return(is_user_type($T)?idf_escape($T).substr($n["full_type"],strlen($T)):$n["full_type"]);}function
is_searchable(array$n,array$W){if(!isset($n["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$W["op"]))return
true;$T=$n["type"];$al=$W["val"];$Za='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Za~",$T))return
false;if(preg_match(number_type(),$T)){$hi='-?\d+(\.\d+)?';$Bh='';if(preg_match('~IN$~',$W["op"])){$al=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$al);$Bh="( *, *$hi)*";}elseif($W["op"]=="BETWEEN")$Bh="\\s+AND\\s+$hi";return(bool)preg_match('~^'.$hi.$Bh.'$~i',$al);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$al);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$al);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$al);return
true;}function
remove_slashes(array$Y,$he=false){$H=array();foreach($Y
as$w=>$W)$H[stripslashes($w)]=(is_array($W)?remove_slashes($W,$he):($he?$W:stripslashes($W)));return$H;}function
bracket_escape($t,$Sa=false){static$cn=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Sa?array_flip($cn):$cn));}function
url_escape($P){static$cn=array();if(!$cn){$cn=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$ob)$cn[$ob]=sprintf('%%%02X',ord($ob));for($r=0;$r<256;$r++){if($r<32||$r>126)$cn[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$P,$cn);}function
min_version($bo,$Sg="",$h=null){$h=connection($h);$xl=$h->server_info;if($Sg&&preg_match('~([\d.]+)-MariaDB~',$xl,$_)){$xl=$_[1];$bo=$Sg;}return$bo&&version_compare($xl,$bo)>=0;}function
charset(Db$g){return(min_version("5.5.3",0,$g)?"utf8mb4":"utf8");}function
ini_set($Fi,$X){return(function_exists('ini_set')?\ini_set($Fi,$X):false);}function
ini_bool($Nf){$W=ini_get($Nf);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($Nf){$W=ini_get($Nf);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($I,$Xi){$Wg=(int)ini_get("max_input_vars");return($Wg?(int)floor(($Wg-$Xi)/$I):0);}function
max_input_vars_error(){$Nf="max_input_vars";return
lang(1,"<b>$Nf = ".ini_get($Nf)."</b>");}function
sid(){static$H;if($H===null)$H=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$H;}function
set_password($ao,$M,$U,$D){$_SESSION["pwds"][$ao][$M][$U]=($_COOKIE["adminer_key"]&&is_string($D)?array(encrypt_string($D,$_COOKIE["adminer_key"])):$D);}function
get_password(){$H=get_session("pwds");if(is_array($H))$H=($_COOKIE["adminer_key"]?decrypt_string($H[0],$_COOKIE["adminer_key"]):false);return$H;}function
get_val($F,$n=0,$Sb=null){$Sb=connection($Sb);$G=$Sb->query($F);if(!is_object($G))return
false;$I=$G->fetch_row();return($I?$I[$n]:false);}function
get_vals($F,$d=0){$H=array();$G=connection()->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[]=$I[$d];}return$H;}function
get_key_vals($F,$h=null,$_l=true){$h=connection($h);$H=array();$G=$h->query($F);if(is_object($G)){while($I=$G->fetch_row()){if($_l)$H[$I[0]]=$I[1];else$H[]=$I[0];}}return$H;}function
get_rows($F,$h=null,$m="<p class='error'>"){$Sb=connection($h);$H=array();$G=$Sb->query($F);if(is_object($G)){while($I=$G->fetch_assoc())$H[]=$I;}elseif(!$G&&!$h&&$m&&(defined('Adminer\PAGE_HEADER')||$m=="-- "))echo$m.adminer()->error()."\n";return$H;}function
unique_array($I,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]&&!$u["invalid"]){$H=array();foreach($u["columns"]as$w){if(!isset($I[$w]))continue
2;$H[$w]=$I[$w];}return$H;}}}function
where_function($Ee,$d,array$n){if($Ee=="md5")return
driver()->md5($d,$n)?:$d;return(in_array($Ee,driver()->functions)||in_array($Ee,driver()->grouping)?apply_sql_function($Ee,$d):$d);}function
where(array$Z,array$o=array()){$H=array();foreach((array)$Z["where"]as$w=>$W){$w=bracket_escape($w,true);$d=idf_escape($w);$n=idx($o,$w,array());$be=$n["type"];$ag=$n&&(is_blob($n)||preg_match('~binary~',$be));$H[]=$d.($ag&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$be=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$n["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="pgsql"&&preg_match('~\.~',$be)?"::text = ".q($W):(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($be,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($n,q($W))))))));if(JUSH=="sql"&&preg_match('~char|text~',$be)&&preg_match("~[^ -@]~",$W))$H[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$H[]=idf_escape($w)." IS NULL";foreach((array)$Z["col"]as$r=>$Db){$W=idx($Z["val"],$r);$H[]=where_function(idx($Z["fun"],$r),idf_escape($Db),idx($o,$Db,array())).($W!==null?" = ".q($W):" IS NULL");}return
implode(" AND ",$H);}function
where_columns(array$o){$H=array();foreach((array)$_GET["null"]as$w)$H[$w]=true;foreach(array_keys((array)$_GET["where"])as$w)$H[bracket_escape($w,true)]=true;foreach((array)$_GET["col"]as$Db)$H[$Db]=true;return
array_intersect_key($H,$o);}function
where_check($W,array$o=array()){parse_str($W,$rb);remove_slashes(array(&$rb));return
where($rb,$o);}function
where_link($r,$d,$X,$Ci="="){$_i=($X!==null?$Ci:"IS NULL");return"&where[$r][col]=".url_escape($d).($_i!=first(adminer()->operators())?"&where[$r][op]=".url_escape($_i):"")."&where[$r][val]=".url_escape($X);}function
convert_fields(array$e,array$o,array$L=array()){$H="";foreach($e
as$w=>$W){if($L&&!in_array(idf_escape($w),$L))continue;$Ha=convert_field($o[$w]);if($Ha)$H
.=", $Ha AS ".idf_escape($w);}return$H;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($A,$X,$Gg=2592000){header("Set-Cookie: $A=".rawurlencode($X).($Gg?"; expires=".gmdate("D, d M Y H:i:s",time()+$Gg)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($A=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
php_error_text($m){return(ini_bool("html_errors")?html_entity_decode(strip_tags($m)):$m);}function
get_url($Kn,$bc){$http_response_header=null;$Cd=array();set_error_handler(function($_d,$m)use(&$Cd){$Cd[]=preg_replace('~^file_get_contents\([^)]*\)( \[[^]]*\])?:\s*~','',php_error_text($m));return
true;});$H=file_get_contents($Kn,false,$bc);restore_error_handler();$ef=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($H,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ef,0,''),$_)?$_[1]:''),(array)$ef,($H===false?implode("\n",$Cd):''),);}function
json_decode_exact($ig){$ig=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$ig);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$ig));}function
json_scalar($W){return(is_string($W)&&substr($W,0,1)=="\1"?substr($W,1):$W);}function
json_encode_exact($W,$ne=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($W,$ne));}function
get_settings($fc){parse_str($_COOKIE[$fc],$Al);return$Al;}function
get_setting($w,$fc="adminer_settings",$l=null){return
idx(get_settings($fc),$w,$l);}function
save_settings(array$Al,$fc="adminer_settings"){$X=http_build_query($Al+get_settings($fc));cookie($fc,$X);$_COOKIE[$fc]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($qe=false){$Nn=ini_bool("session.use_cookies");if(!$Nn||$qe){session_write_close();if($Nn&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$W){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($ao,$M,$U,$k=null){$Jn=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($k!==null?"db|":"").($ao=='mssql'||$ao=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Jn,$_);return"$_[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($ao!="server"||$M!=""?url_escape($ao)."=".url_escape($M)."&":"")."username=".url_escape($U).($k!=""?"&db=".url_escape($k):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Og,$mh=null){if($mh!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Og!==null?$Og:$_SERVER["REQUEST_URI"]))][]=$mh;}if($Og!==null){if($Og=="")$Og=".";header("Location: $Og");exit;}}function
query_redirect($F,$Og,$mh,$pk=true,$Kd=true,$Vd=false,$Om=""){if($Kd){$Wl=microtime(true);$Vd=!connection()->query($F);$Om=format_time($Wl);}$Pl=($F?adminer()->messageQuery($F,$Om,$Vd):"");if($Vd){adminer()->error
.=adminer()->error().$Pl.script("messagesPrint();")."<br>";return
false;}if($pk)redirect($Og,$mh.$Pl);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($F){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$F:(preg_match('~;$~',$F)?"DELIMITER ;;\n$F;\nDELIMITER ":$F).";");}function
queries($F){remember_query($F);return
connection()->query($F);}function
apply_queries($F,array$S,$Ed='Adminer\table'){foreach($S
as$Q){if(!queries("$F ".$Ed($Q)))return
false;}return
true;}function
queries_redirect($Og,$mh,$pk){$hk=implode("\n",Queries::$queries);$Om=format_time(Queries::$start);return
query_redirect($hk,$Og,$mh,$pk,false,!$pk,$Om);}function
format_time($Wl){return
lang(2,max(0,microtime(true)-$Wl));}function
relative_uri($Jn=''){return
preg_replace_callback('~^[^?]*~',function($_){return
str_replace(":","%3A",$_[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Jn?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($ej=""){return
substr(preg_replace("~(?<=[?&])($ej".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($A,$zc=false){$de=$_FILES[$A];if(!$de)return
null;foreach($de
as$w=>$W)$de[$w]=(array)$W;$H=array();foreach($de["error"]as$w=>$m){if($m)return$m;$p=$de["name"][$w];$Wm=$de["tmp_name"][$w];$Zb=file_get_contents($zc&&preg_match('~\.gz$~',$p)?"compress.zlib://$Wm":$Wm);if($zc){$Wl=substr($Zb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Wl))$Zb=iconv("utf-16","utf-8",$Zb);elseif($Wl=="\xEF\xBB\xBF")$Zb=substr($Zb,3);}$H[]=array($p,$Zb);}return$H;}function
get_file($w,$zc=false,$Gc=""){$ge=get_files($w,$zc);if(!is_array($ge))return$ge;$H='';foreach($ge
as$de){$Zb=$de[1];$H
.=$Zb;if($Gc)$H
.=(preg_match("($Gc\\s*\$)",$Zb)?"":$Gc)."\n\n";}return$H;}function
upload_error($m){$eh=($m==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($m?lang(3).($eh?" ".lang(4,$eh):""):lang(5));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
utf8_length($W){return
strlen(preg_replace('~[\x80-\xBF]~','',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$_);$El=strlen($_[3]);$H=number_format($W,0,".","");$H=preg_replace('~\B(?=(\d{'.(strlen($_[2])?:$El).'})*\d{'.$El.'}$)~',$_[1],$H);return
strtr($H,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$w){$W=idx($R,$w,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$Ca=($w=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$W=in_array($w,array("Data_length","Index_length","Data_free"))?adminer()->formatSizeValue($W):format_number($W);return($Ca?"~ ":"").$W;}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Wd=false){$H=table_status($Q,$Wd);return($H?reset($H):array("Name"=>$Q));}function
column_foreign_keys($Q){$H=array();foreach(adminer()->foreignKeys($Q)as$q){foreach($q["source"]as$W)$H[$W][]=$q;}return$H;}function
fields_from_edit(){$H=array();foreach((array)$_POST["field_keys"]as$w=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$w];$_POST["fields"][$W]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$W){$A=bracket_escape($w,true);$H[$A]=array("field"=>$A,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($A==driver()->primary),);}return$H;}function
dump_headers($sf,$Fh=false){$H=adminer()->dumpHeaders($sf,$Fh);$Zi=$_POST["output"];if($Zi!="text"||$H=="tar"){$Ob=($Zi!="text"&&$Zi!="file"&&preg_match('~^[0-9a-z]+$~',$Zi)?".$Zi":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($sf).".$H$Ob");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$H;}function
dump_csv(array$I){$pn=$_POST["format"]=="tsv";foreach($I
as$w=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($pn?'\t':'[,;]|^$').'~',$W))$I[$w]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($pn?"\t":";")),$I)."\r\n";}function
parse_csv($nc,$ll){$H=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$nc,$Ug);foreach($Ug[0]as$I){preg_match_all("~((?>\"[^\"]*\")+|[^$ll]*)$ll~",$I.$ll,$Vg);$H[]=$Vg[1];}return$H;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?idf_unescape($W):$W);}function
apply_sql_function($Fe,$d){return($Fe?($Fe=="unixepoch"?"DATETIME($d, '$Fe')":($Fe=="count distinct"?"COUNT(DISTINCT ":strtoupper("$Fe("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($p){if(is_link($p))return;$xe=@fopen($p,"c+");if(!$xe)return;@chmod($p,0660);if(!flock($xe,LOCK_EX)){fclose($xe);return;}return$xe;}function
file_write_unlock($xe,$rc){rewind($xe);fwrite($xe,$rc);ftruncate($xe,strlen($rc));file_unlock($xe);}function
file_unlock($xe){flock($xe,LOCK_UN);fclose($xe);}function
first(array$Ga){return
reset($Ga);}function
password_file($i){$p=get_temp_dir()."/adminer.key";if(!$i&&!file_exists($p))return'';$xe=file_open_lock($p);if(!$xe)return'';$H=stream_get_contents($xe);if(!$H){$H=rand_string();file_write_unlock($xe,$H);}else
file_unlock($xe);return$H;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$z,array$n,$Mm,array$_j=array()){if(is_array($W)){$H="";if(array_filter($W,'is_array')==array_values($W)){$ng=array();foreach($W
as$V)$ng+=array_fill_keys(array_keys($V),null);foreach(array_keys($ng)as$kg)$H
.="<th>".h($kg);foreach($W
as$V){$H
.="<tr>";foreach(array_merge($ng,$V)as$Un)$H
.="<td>".select_value($Un,$z,$n,$Mm,$_j);}}else{foreach($W
as$kg=>$V)$H
.="<tr>".($W!=array_values($W)?"<th>".h($kg):"")."<td>".select_value($V,$z,$n,$Mm,$_j);}return"<table>$H</table>";}if(!$z)$z=adminer()->selectLink($W,$n);if($z===null){if(is_mail($W))$z="mailto:$W";if(is_url($W))$z=$W;}$W=driver()->value($W,$n);$H=adminer()->editVal($W,$n);if($H!==null){if(!is_utf8($H))$H="\0";elseif($Mm!=""&&is_shortable($n))$H=shorten_utf8($H,max(0,+$Mm),"",$_j);else$H=highlight_matches($H,$_j);}return
adminer()->selectVal($H,$z,$n,$W);}function
is_blob(array$n){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$n["type"])&&!in_array($n["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$n){return$n["auto_increment"]&&(JUSH=="mssql"||$n["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($pd){$Ja='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Zc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$zj="$Ja+(\\.$Ja+)*@($Zc?\\.)+$Zc";return
is_string($pd)&&preg_match("(^$zj(,\\s*$zj)*\$)i",$pd);}function
is_url($P){$Zc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Zc?\\.)+$Zc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$P);}function
is_ipv6($pa){$Me='[\da-f]{1,4}';$Zf='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($Me:){7}$Me|($Me:){6}$Zf|(($Me:)*$Me)?::(($Me:)*($Me|$Zf))?)$~iD",$pa);}function
is_shortable(array$n){return!preg_match('~'.number_type().'|date|time|year~',$n["type"]);}function
url_host($of){return(strpos($of,":")!==false?"[$of]":$of);}function
server_parts(array$tj){return
array("scheme"=>(string)$tj["scheme"],"host"=>(string)$tj["host"],"port"=>(string)$tj["port"],"socket"=>(string)$tj["socket"],"path"=>(string)$tj["path"],);}function
parse_server($M){if($M=="")return
server_parts(array());if($M[0]==":"&&!is_ipv6($M)){$Ck=substr($M,1);if(preg_match('~^\d+$~D',$Ck))return
server_parts(array("port"=>$Ck));return(preg_match('~^/[-\w.:/]*$~D',$Ck)?server_parts(array("socket"=>$Ck)):null);}$Yk="";if(preg_match('~^([-+.\w]+)://~',$M,$_)){$Yk=strtolower($_[1]);$M=substr($M,strlen($_[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$M,$_))return(is_ipv6($_[1])?server_parts(array("scheme"=>$Yk,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);if(is_ipv6($M))return
server_parts(array("scheme"=>$Yk,"host"=>$M));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$M,$_))return
server_parts(array("scheme"=>$Yk,"host"=>$_[1],"port"=>$_[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$M,$_)?server_parts(array("scheme"=>$Yk,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);}function
count_rows($Q,array$Z,$bg,array$Me){$F=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($bg&&(JUSH=="sql"||count($Me)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$Me).")$F":"SELECT COUNT(*)".($bg?" FROM (SELECT 1$F GROUP BY ".implode(", ",$Me).") x":$F));}function
slow_query($F){$k=adminer()->database();$Pm=adminer()->queryTimeout();$Gl=driver()->slowQuery($F,$Pm);$h=null;if(!$Gl&&support("kill")){$h=connect();if($h&&($k==""||$h->select_db($k))){$og=number(get_val(connection_id(),0,$h));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$og&token=".get_token()."'); }, 1000 * $Pm);");}}ob_flush();flush();$H=@get_key_vals(($Gl?:$F),$h,false);if($h){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$H;}function
get_token(){$mk=rand(1,1e6);return($mk^$_SESSION["token"]).":$mk";}function
verify_token(){list($Xm,$mk)=explode(":",$_POST["token"]);return($mk^$_SESSION["token"])==$Xm&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$Nc=""){$_a=array_flip(str_split(compress_alphabet()));$x=strlen($P);$Xn=($x?13*($x-1)/2-$_a[$P[0]]:0);$Za="";$Ck=0;$Dk=0;for($r=1;$r<$x;$r+=2){$Ck=($Ck<<13)+$_a[$P[$r]]*93+$_a[$P[$r+1]];$Dk+=13;while($Dk>=8&&$Xn>=8){$Dk-=8;$Xn-=8;$Za
.=chr($Ck>>$Dk);$Ck&=(1<<$Dk)-1;}}if($Za=="")return"";if($Nc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$Nc)),$Za,ZLIB_FINISH);return($Nc==""&&function_exists('gzinflate')?gzinflate($Za):inflate($Za,$Nc));}function
inflate($Za,$Nc=""){$Dg=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Eg=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Rc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Tc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$H=$Nc;$E=0;do{$ie=inflate_bits($Za,$E,1);$T=inflate_bits($Za,$E,2);if(!$T){$E=($E+7)&~7;$x=inflate_bits($Za,$E,16);$E+=16;$H
.=substr($Za,$E>>3,$x);$E+=$x<<3;}else{if($T==1){$Mg=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Uc=array_fill(0,30,5);}else{$Lg=inflate_bits($Za,$E,5)+257;$Sc=inflate_bits($Za,$E,5)+1;$Ki=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$th=array_fill(0,19,0);$sh=inflate_bits($Za,$E,4)+4;for($r=0;$r<$sh;$r++)$th[$Ki[$r]]=inflate_bits($Za,$E,3);$uh=inflate_table($th);$Fg=array();while(count($Fg)<$Lg+$Sc){$lm=inflate_symbol($Za,$E,$uh);if($lm==16)$Fg=array_merge($Fg,array_fill(0,inflate_bits($Za,$E,2)+3,end($Fg)));elseif($lm==17)$Fg=array_merge($Fg,array_fill(0,inflate_bits($Za,$E,3)+3,0));elseif($lm==18)$Fg=array_merge($Fg,array_fill(0,inflate_bits($Za,$E,7)+11,0));else$Fg[]=$lm;}$Mg=array_slice($Fg,0,$Lg);$Uc=array_slice($Fg,$Lg);}$Ng=inflate_table($Mg);$Wc=inflate_table($Uc);while(($lm=inflate_symbol($Za,$E,$Ng))!=256){if($lm<256)$H
.=chr($lm);else{$x=$Dg[$lm-257]+inflate_bits($Za,$E,$Eg[$lm-257]);$Vc=inflate_symbol($Za,$E,$Wc);$B=strlen($H)-$Rc[$Vc]-inflate_bits($Za,$E,$Tc[$Vc]);for($r=0;$r<$x;$r++)$H
.=$H[$B+$r];}}}}while(!$ie);return($Nc==""?$H:substr($H,strlen($Nc)));}function
inflate_bits($Za,&$E,$hc){$H=0;for($r=0;$r<$hc;$r++){$H+=((ord($Za[$E>>3])>>($E&7))&1)<<$r;$E++;}return$H;}function
inflate_table(array$Fg){$Q=array();$Cb=0;for($ab=1;$ab<=max($Fg);$ab++){foreach($Fg
as$lm=>$x){if($x==$ab){$Q[$ab][$Cb]=$lm;$Cb++;}}$Cb<<=1;}return$Q;}function
inflate_symbol($Za,&$E,array$Q){$Cb=0;$ab=0;do{$Cb=($Cb<<1)+inflate_bits($Za,$E,1);$ab++;}while(!isset($Q[$ab][$Cb]));return$Q[$ab][$Cb];}function
script($Ll,$bn="\n"){return"<script".nonce().">$Ll</script>$bn";}function
script_src($Kn,$Cc=false){return"<script src='".h($Kn)."'".nonce().($Cc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Fd,$Ve,$Da=null){$Fa=array();foreach(array_slice(func_get_args(),2)as$W)$Fa[]=json_encode($W,256);return" data-on$Fd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Ve(".implode(", ",$Fa).")")."'";}function
input_hidden($A,$X=""){return"<input type='hidden' name='".h($A)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($A,$X,$ub,$sg="",$c="",$_b="",$ug=""){$H="<input type='checkbox' name='$A' value='".h($X)."'".($ub?" checked":"").($sg==""&&$_b?" class='$_b'":"").($ug?" aria-labelledby='$ug'":"").$c.">";return($sg!=""?"<label".($_b?" class='$_b'":"").">$H".h($sg)."</label>":$H);}function
optionlist($Hi,$hl=null,$On=false){$H="";foreach($Hi
as$kg=>$V){$Ji=array($kg=>$V);if(is_array($V)){$H
.='<optgroup label="'.h($kg).'">';$Ji=$V;}foreach($Ji
as$w=>$W)$H
.='<option'.($On||is_string($w)?' value="'.h($w).'"':'').($hl!==null&&($On||is_string($w)?(string)$w:$W)===$hl?' selected':'').'>'.h($W);if(is_array($V))$H
.='</optgroup>';}return$H;}function
group_system(array$Nh,$Xk=false){$H=array();$mm=array();foreach($Nh
as$A){if($Xk?driver()->isSystem(DB,$A):driver()->isSystem($A))$mm[]=$A;else$H[]=$A;}if($mm)$H[lang(8,'')]=$mm;return$H;}function
html_select($A,array$Hi,$X="",$c="",$ug=""){static$sg=0;$tg="";if(!$ug&&substr($Hi[""],0,1)=="("){$sg++;$ug="label-$sg";$tg="<option value='' id='$ug'>".h($Hi[""]);unset($Hi[""]);}return"<select name='".h($A)."'".($ug?" aria-labelledby='$ug'":"")."$c>".$tg.optionlist($Hi,$X)."</select>";}function
html_radios($A,array$Hi,$X="",$ll=""){$H="";foreach($Hi
as$w=>$W)$H
.="<label><input type='radio' name='".h($A)."' value='".h($w)."'".($w==$X?" checked":"").">".h($W)."</label>$ll";return$H;}function
confirm($mh=""){return
on('click','confirmClick',$mh?:lang(9));}function
print_fieldset($s,$Cg,$eo=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$Cg</a>","</legend>","<div id='fieldset-$s'".($eo?"":" class='hidden'").">\n";}function
bold($bb,$_b=""){return($bb?" class='active $_b'":($_b?" class='$_b'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($C){return
remove_from_uri("page|next").($C?"&page=$C".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($C,$oc){return" ".($C==$oc?($C?"<b>".($C+1)."</b>":$C+1):'<a href="'.h(pagination_href($C)).'">'.($C+1)."</a>");}function
hidden_fields(array$ak,array$xf=array(),$Pj=''){$H=false;foreach($ak
as$w=>$W){if(!in_array($w,$xf)){if(is_array($W))hidden_fields($W,array(),$w);else{$H=true;echo
input_hidden(($Pj?$Pj."[$w]":$w),$W);}}}return$H;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$In){$In=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($In?on('submit','uploadProgress',ME."upload=$In",SESSION_NAME."=$In"):"");}function
file_input($c,$Ck=""){$Yg="max_file_uploads";$Zg=ini_get($Yg);$eh="upload_max_filesize";$fh=ini_bytes($eh);$Lj=ini_bytes("post_max_size");if($Lj&&$Lj<$fh){$eh="post_max_size";$fh=$Lj;}$gh=ini_get($eh);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$Zg,lang(10,"$Yg = $Zg"),$fh,lang(10,"$eh = $gh")).">$Ck":lang(11));}function
enum_input($T,$c,array$n,$X,$sd=""){preg_match_all("~".driver()->enumLength."~",$n["length"],$Ug);$Pj=($n["type"]=="enum"?"val-":"");$ub=(is_array($X)?in_array("null",$X):$X===null);$H=($n["null"]&&$Pj?"<label><input type='$T'$c value='null'".($ub?" checked":"")."><i>$sd</i></label>":"");foreach($Ug[0]as$W){$W=stripcslashes(idf_unescape($W));$ub=(is_array($X)?in_array($Pj.$W,$X):$X===$W);$H
.=" <label><input type='$T'$c value='".h($Pj.$W)."'".($ub?' checked':'').'>'.h(adminer()->editVal($W,$n)).'</label>';}return$H;}function
input(array$n,$X,$Fe,$Qa=false,$Fn=false){$A=h(bracket_escape($n["field"]));echo"<td class='function'>";$zd=driver()->enumLength($n);if($zd){$n["type"]="enum";$n["length"]=$zd;}$Hi=($n["type"]=="enum"||$n["type"]=="set");if(is_array($X)&&!$Fe&&!$Hi)$Fe="json";$ig=($Fe=="json"||preg_match('~^jsonb?$~',$n["full_type"]));if($ig&&$X!=''&&(JUSH!="pgsql"||$n["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=(is_array($X)?json_encode($X,128|64|256):json_encode_exact(json_decode_exact($X),128|64|256));$Bk=($Fn&&is_identity_always($n));if($Bk&&!$_POST["save"])$Fe=null;$Ge=(isset($_GET["select"])||$Bk?array("orig"=>lang(12)):array())+adminer()->editFunctions($n);$c=" name='fields[$A]".($Hi?"[]":"")."'".($Qa?" autofocus":"");echo
driver()->unconvertFunction($n)." ";$Q=$_GET["edit"]?:$_GET["select"];if($n["type"]=="enum")echo
h($Ge[""])."<td>".adminer()->editInput($Q,$n,$c,$X);else{$Xe=(in_array($Fe,$Ge)||isset($Ge[$Fe]));$je=0;foreach($Ge
as$w=>$W){if($w===""||!$W)break;$je++;}echo(count($Ge)>1?"<select name='function[$A]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Ge,$Fe===null||$Xe?$Fe:"")."</select>":h(reset($Ge)))."<td".($je&&count($Ge)>1?on('input','skipOriginal',$je):"").">";$Pf=adminer()->editInput($Q,$n,$c,$X);if($Pf!="")echo$Pf;elseif(preg_match('~bool~',$n["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($n["type"]=="set")echo
enum_input("checkbox",$c,$n,(is_string($X)?explode(",",$X):$X));elseif(is_blob($n)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'>";elseif($ig)echo"<textarea$c cols='50' rows='12' class='jush-json'>\n".h($X).'</textarea>';elseif(($Lm=preg_match('~text|lob|memo~i',$n["type"]))||preg_match("~\n~",$X)){if($Lm&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$J=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$J'";}echo"<textarea$c>\n".h($X).'</textarea>';}else{$un=driver()->types();$sn=$un[$n["type"]];$Ga=preg_match('~\[]~',$n["full_type"]);if($Ga)$hh=0;elseif(preg_match('~date|time|year~',$n["type"])){$Oj=($n["length"]==""&&JUSH=="pgsql"?6:$n["length"]);$ye=(preg_match('~time~',$n["type"])&&preg_match('~^[1-9]\d*$~',$Oj)?$Oj+1:0);$hh=($sn?$sn+$ye:0);}elseif(!preg_match('~int|vector~',$n["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$n["length"],$_))$hh=(preg_match("~binary~",$n["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$n["unsigned"]?1:0);else$hh=($sn?$sn+($n["unsigned"]?0:1):0);echo"<input".((!$Xe||$Fe==="")&&preg_match('~^'.int_type().'$~',$n["type"])&&!$Ga?" type='number'":"")." value='".h($X)."'".($hh?" data-maxlength='$hh'":"").(preg_match('~char|binary~',$n["type"])&&$hh>20?" size='".($hh>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$n,$X),(count($Ge)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$n){$t=bracket_escape($n["field"]);$Fe=idx($_POST["function"],$t);if($Fe=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?idf_escape($n["field"]):false);if($Fe=="NULL")return"NULL";if(is_blob($n)&&ini_bool("file_uploads")){$de=get_file("fields-$t");if(!is_string($de))return
false;return
driver()->quoteBinary($de);}$X=idx($_POST["fields"],$t);if($X===null)return
false;if($n["type"]=="enum"||driver()->enumLength($n)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($n["auto_increment"]&&$X=="")return
null;if($n["type"]=="set")$X=implode(",",(array)$X);if($Fe=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($n,$X,$Fe);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$kl="<ul>\n";foreach(table_status('',true)as$Q=>$R){$A=adminer()->tableName($R);if(isset($R["Engine"])&&$A!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$G=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$G||$G->fetch_row()){$Wj="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$A</a>";echo"$kl<li>".($G?$Wj:"<p class='error'>$Wj: ".adminer()->error())."\n";$kl="";}}}echo($kl?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($Lm,$Dl=0){return
on('mouseover','helpMouseover',$Lm,$Dl).on('mouseout','helpMouseout');}function
on_help_value($wk="",$Ak=""){return
on('mouseover','helpValueMouseover',$wk,$Ak).on('mouseout','helpMouseout');}function
edit_form($Q,array$o,$I,$Fn,$m='',$F='',$Om=''){$um=adminer()->tableName(table_status1($Q,true));page_header(($Fn?lang(14):lang(15)),$m,array("select"=>array($Q,$um)),$um);adminer()->editRowPrint($Q,$o,$I,$Fn,$F,$Om);if($I===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$nd=false;$lo=($Fn&&!isset($_GET["select"])?where_columns($o):array());$cc=(count($lo)!=count($o));if(!$cc)$lo=array();if(!$o)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Qa=!$_POST;foreach($o
as$A=>$n){echo"<tr".($lo[$A]?on('change','whereChange'):"")."><th>".adminer()->fieldName($n);$l=idx($_GET["set"],bracket_escape($A));if($l===null){$l=$n["default"];if($n["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$l,$yk))$l=$yk[1];if(JUSH=="sql"&&preg_match('~binary~',$n["type"]))$l=bin2hex($l);}$X=($I!==null?($n["type"]=="set"&&is_array($I[$A])?implode(",",$I[$A]):(is_bool($I[$A])?+$I[$A]:$I[$A])):(!$Fn&&$n["auto_increment"]?"":(isset($_GET["select"])?false:$l)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$n);if(($Fn&&!isset($n["privileges"]["update"]))||$n["generated"])echo"<td class='function'><td>".select_value($X,'',$n,null);else{$nd=true;$Fe=($_POST["save"]?idx($_POST["function"],bracket_escape($A),""):($Fn&&preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$Fn&&$X==$n["default"]&&($n["default_sql"]||preg_match('~^[\w.]+\(~',$X)))$Fe="SQL";if(preg_match("~time~",$n["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$Fe=(in_array("now",adminer()->editFunctions($n))?"now":"current_timestamp");}if(preg_match("~date~",$n["type"])&&preg_match('~^CURRENT_DATE~i',$X)){$X="";$Fe=(in_array("now",adminer()->editFunctions($n))?"now":"current_date");}if($n["type"]=="uuid"&&$X=="uuid()"){$X="";$Fe="uuid";}if($Qa!==false)$Qa=($n["auto_increment"]||$Fe=="now"||$Fe=="uuid"?null:true);input($n,$X,$Fe,$Qa,$Fn);if($Qa)$Qa=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($nd){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$cc){$Oc=($lo&&($m!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Fn?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$Oc".($Fn?on('click','ajaxForm',lang(21)):"").">\n";}}echo($Fn?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($zj,$x){return
str_repeat("$zj{0,65535}",$x/65535)."$zj{0,".($x%65535)."}";}function
shorten_utf8($P,$x=80,$gm="",array$_j=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$P,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$P,$_);$x=strlen(isset($_[2])?$_[1]:preg_replace('~\n[^\n]*\z~',"\n",$_[1]));return
highlight_matches($P,$_j,$x).$gm.(isset($_[2])?"":"<i>…</i>");}function
highlight_matches($P,array$_j,$x=null){if($x===null)$x=strlen($P);$H="";$E=0;if($_j&&@preg_match_all("((?|".implode("|",$_j)."))su",$P,$Ug,PREG_OFFSET_CAPTURE)){foreach($Ug[0]as$_){list($Lm,$Wl)=$_;if($Lm!=""&&$Wl<$x){$ud=min($Wl+strlen($Lm),$x);$H
.=h(substr($P,$E,$Wl-$E))."<mark>".h(substr($P,$Wl,$ud-$Wl))."</mark>";$E=$ud;}}}return$H.h(substr($P,$E,$x-$E));}function
icon($rf,$A,$qf,$Rm,$c=""){return"<button ".($A?"type='submit' name='$A'":"draggable='true' tabindex='-1'")." title='".h($Rm)."' class='icon icon-$rf".($A?"":" jsonly")."'$c><span>$qf</span></button>";}function
copy_icon(){$gc=lang(23);return"<a href='' class='jsonly icon-copy' title='$gc'><span>$gc</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(',c(<UcrZ9FloAdLIHT,e^12P>vJpDt~s)jpQ5n*/jNlpj4]q19=eVp+w8a%oBQUTAw)IeW/u7lvs|iu0YUhhis?by`^2y`w^^.@t-OS(=WOR|CP&H43h,8fIox:9}+Dr41T<m4m%{PjoXQ&uX>x]gL$Cv>O&?K0e2t6KKZc]hR1[7PmhM1DHfTAWT:%[6bp<T/81fB
.r29*O)#Tst!3PTs7R./CjgEHA#(jbnyfb<iJlUvk{igwY622G;UTZa%AjWa=un-JD;P"b3c<*O>GW;|by+@<eF.,aIW0o9w#7A?P6hx<(,;THCs"hN+JLZ8"S8uS$H?@Mn-Ex6P@.*Y"8*e4u2{IWt{GJ`8QH/*!]T>-TFc9G$kOi9`vgYxwn
c%g)X-E
)EzoS79gZ,B^(JY>CV+i%U#DXw(U!jShr7E.,O/H=?y_RTf@C
um+TP6JsRX*6UHOW)XH&`0uU-%[B_5b/PGK%V6F"*VOYlO%r~e%7r4zIiZe)OGZ+6pFLA:V,Br4Lp[>S/$K&z12m)VZY9)vPI5?F%YP7Ogq>&-5];3(c`Imh^fc!C3hse,H43n$0!iXAQqbf`(tm1orwwDZb+`#pZ;K#kLB(y*uQDg@<,=2_=(-jcNo-p6l/mQFggd1JnOB>B-p/=ZD<=<t;t.KXa4sgH)Ltuj>H,".5g`>k8G`jTY]_-$T]X^Ytg4B+4Pe5dn[0Jmv0vEi+(l~M}[
k-y`fw,^#?q2G;jQ#Se!4D;B@:uF+ui]U<[N^fhhFUN5c%$N+yT,Q9)A"}di>"HNulNmg7&eH$fncswB:21#8WH~dZ.B4u[RQYCj17"f:w(Ftt+[2v*CdE?AKm]3XAUA/"$*$Hx-`6G@)skomr,jBx3je[fAY!+&;z7"<6tq,{mH[(s
CZ.eg*0.$Z[ctG`C;2yvj};T_:TBs[DdMHH;"3prfxZj3
U3e%Z"
N>j@wO55C[8"f&k2gZj[Q:m;xHD7ZJQBzlbSC;1B87#?eSY06Y65k!fOU6_!*Wx:h2q-(T&.*g0OSNSp]kAIgfoTd7i*{J1gg
0YX[M%&kFnF!owS$[QI4)#U=a"[;5oLjT;#D[I1sFRw#|h/g4eM:m%T,SUdq_rWvCQ+@=3hZVZUnr82v==?Rh?#bz&o4$U_6N6)0WPGCy>@WB2tyW9Rvy4j3+.nL(0e:5hy9?UE[27WG9ri*kXwFC-:iQ$"0?d!DJ_A5FHzo<^X+//:a;o|-:)5067zUG^N#rr$qI"K3Q0Xdjg_CG:s(Arl+%Hr)}I5K}9[CJw8qYgb"JY|H#,CZd;l8ef/9hi$fBrt3InM_uo+%kThD;YF5R-(i$n2Q6WR3DTRd|b"9d;7y~"Gt0x]NewuUp^RT$]H8?OEUD4t1{>B?u3"c&.2So29(`lG5I0F?J]*Ai?TgR)%y}q:BvlK%7lqi8N8aATIByV*OrWO,U-__]e!Ts(.tI!v[=1g-0$D:2Zl^e_7PF>e?,FN4_&RsZ=]c-:?ICbC&Zk+vq2F3iEN9cq*##I=Fy@N>$2/={hUbQ[Q%
9XvUvMh<Njh(4i^u_0%RI(<Q-([F
JO39W9H8brm;?_!1ov4P:i,suDlpy`5!*.#@`/c
*=R>+2ZO$IAog/083.+hmP;I|sToi7w=1k},|0v&jyZf8,e[cHgD[^S,#pE
O7O6$7bWPM&P
#{mOOuY<0N=0/}8;_1*pPz`fF;d*^Xs~E`I$,E.eJh-}gkVW#(ZgifL&ku5^Um"P9?lD/t%YRj1`>H)-*Z(uqlXTp{l|6#"JT_-Glw*koUuh"1oVQ3c:q`e(Ou;1f.ML1?
+s*ZYj[Qyz!@k10A9>P?:uQy3r5lp6SS5Cw&bAcsPdf-?4Pn/lw(zfj$CIY**c+;#Ey#G%A#-LO9qG`;!Q$d?g/RT;.;#Jg-&5#c4+xKX>3aOGIOK5g.1&i1qC=5ctCP5w*GWOJJ3nBlcOUQ&ghC^]Lfk>[1IvfA+@Qnx7x-2e`=V->-`sATlbT;jm&Av`@(2b)[HuUYwi.d@B;[+5H-g?od@X?Ftr1dMb{L5LqO5[MV~]!0.@ZTZ+x,,Q$BARIm
h@hLPR&jgL+kr;%KQu*a&qK*s5;#"v?e%lK;`wo+J_/6Zl%Ks>:|B6rsAu]AJb,A8>>u^neEbPK8e%q{,OYFf,eK2W,e;=Sj&DsZ;rd*+-(ISV"Z<#!
I3)4(/n($*PXlC3"juk.,g[eCju"B]4)(!8
VPkw&`](PnO=i*dE[v51@.@.;&1oaqDR?T09$atN?[#E!0dVODQL/,
BQ{+-4SPW1y""2?.e_JUNua[{yx/]wYvW.gva;M5Kjr^0`w34l^Fz5"HnDC["&M_MH;x3H((+I1?qfqFaH^Dkrty?ZJ]cxZUg3sjkeU/hBvu7)bV^l}^}FKH}:=V)3p;X]T&5ez"GU-^zA@e}K.0,*0/MI^*Lju
2LFaQ"h$(F8fG[_UE)@YLWTw|G=t#[$0E"T)@B=ic;G+?K(Tp$Cn,XyT=ksXNR%A?9z@CSo2]8jb8X>VC#r$}hCoey#e?#n4J-XScK<=W4(k"xtQ`4/kEOsp;H%"k%)8^D2Z123^V%v7=.^EUBwvU:u^Vxlfbtq8-iQ-]7HTcxYjtn?y-u0GTq
wp]^pen
U99`GHj#xFV5MZ%|Mb1:f!McteBgmGctX_uWcZh0yj
-BagRa~aVtIohyF!<DR4Vi8I-a%1FHOiTx1G{?,a.vqBUSHCcy2]/_8k_pB:)0i:h27GmUes4E5LO7V[{uza]nh0MRLc$S<l1/^fkBxj;[)z(DAz$#351rq/r;rEdyx2F/-7%HgbAO#PQvW"HJ1j928ZIG]H:j9g0R#t!ylx;w>w>L/D^.}Gec,dWBgw}i{:}6J+JWK!J2<WI
jx*7H]F[_LodM&3e2V9ClhnH=,#m^MrXCK$==0"+vs,8Fu2^iB}Df1uf5g]2:3tw$x#Mcb{0uCjJ}DGbxZefXH0v
VmmzSOrX?`2%D;)2HK,D9HsXy/-D(@kw?LL*1mf1]TQJ&K-.G)37rIQtuaI&!EF]MjyB5L4k$
sA){bj
SJ$<v0gaAU*Z+=*N`"$xR]y/jlpTj5%6Z0
Z2N[+17YTn!;Btc8Lgk/Siv[hI&on{4Vq)h`rM%LC#x2qih>Lttn-b$b^H
G?G-BFJ,i^
WoKzKO*9JgIyk+#V8.tNx~lh-rc`^4:OIv7=TQyc[8s!wP$IEcfCO$oHtQq+y
5m]^w@4}xOw=jqWP62c1Yl){GG6!Cy"::&FxJInJ-ngn5wksWC48^r;7AcS6KoKne(t)HbCElAdGR$V.3)=[y]Ow#_ai]R$*a}Hz7Ofy"J^l)bw"&@bpAh$F/NKht0@|)zwKoE`)N<O32$?-BgND[uv#=Q-ZoYD|cjeCZ@
|AfVIMj1/+o*,ta,vh;0fWp<eP|%rr<5%105iU]AbU25gR=Q9hCO1thcwVPosG8aOCG*]*d7gu5n4?Yuz_Uxn7t>#yZM.3t38D~Bl5TLlUC?p3tkj2/o-^l(?=zxU
&cpoJ7w4RD;0*X"eL8Bkw^MGYA,[w(YEZ>3>Z)qJ->CJx4V19I]wRubX
_w0Ie3OfT/-9";*19V@l0I-/J/LAym^m&Esv!b9r;rDh[Dm.&a1%fvQ|BYH;`@Tk4!n

v,A?3Hi)3MFc6j6ywXs.%8QTf@8H/3@xo*b]|5zwG1+N7bXn<h++/,3@H%=%yedr{w4J+c:Ope$URGK6UJz+CA^DL/[/GQr4a(Yl;^l`c`6Z__M]e]d>Ix}YXyDm/jH^LgAH0b"<|;*P<@8mask^oIs-bh!G,AqnX*(&L^wXEPx^G2=ATA30gwZ
AM~^5Cz)c`Q+fTVlGqV(24fdqMJkcc
Htc/oh(x^Ho6eVS}4n7l2$Jgl^f^;(vtCzm2m^uzdK2h=$/h2fIx:.tf9Z=VeHNay!XBR=EfYV8s[$x"@?t7!mN8/27#QR^HWlA~[DGw2HCKLlixcs(H>YW;4d8L.(#L@2#W?ej",!%Y:Em6R5
[LDp9.MRw+zi45/lEsrgr&aZ"J`_Jso-&sV>dc{Sg+)Qwm!)k:/a(^#+aNxos+>Nx5UT9Na+YC>lUl&Nndk35j
"sF$v=4|#e:<IaPI+33.`8REe<d4rWs*%:33sR7#ry^!jzVrVoa=+>5bBND8`4+?AP$X:m/E5")Z?@#|5%V`o$^bYCRXeWj/a[x%n{
KLVWhANf_5*_aTkgtBZL5HgL!F@XdOSn.*m#*-jM`*v"8sfTaf//F)kEF4(EWc*U*SLZfNNU2j4j`s#&0Pd)hS5#]+b(C]Jf*H9ekiPhOPS3HJYSAb9v-J*4w8YqF+.F}W0!v,XO,,O?Aal_qb3jxt3nlV{X~
FMxK6`a!sMJFIja+FhrMGMZ
{Yy9v"WU(?s
T&1((n{)}/TcRnu?D6N5c]gUyXN5O9$J#Gs1mG2>Un7H1y5NxEx0
g{JsUdtLh%t;E3<~?=RK%~jH-5vO]~;w_{[ygo.p_yb$@_x/O3Uu8FGQ:?Zs6Q5PM[TSpW&=_%L[NJC$-*y,ObV_#NJURX/}4=d{C$$7]q-$.SLFX!w*M.OcBtmF5,SFx@lQJcBrI6wPAs6iAG4;tjveo)');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('(Osc*b3V?$d0U7vYy8!&<6V+#5Y(O]{_E`f_Nnud89X^o)QQ3qBMrdN46cZSQwh=;$0.!*Zw(S_-i>;II-}+0!f6`8I9QVZ;o&E5[c=8273*kW8ozGkc^+xN7#Y.@kai=uf)pXra4.tvBDppE(0O%pyGdHQ;9&iXKL5h"f}sTF^w<u+N`QqVkc//{&C#v2fr*He5[n^S~K}LsnolvKKV$0UQ^5Q%aL,,EO.YcxHfUAFE[fz;NTDXihkd+*v)=*:H<h4m}7:MZ$(T9?)fvQe/+rs-@_!6s.%kFS8BlkTM7:.>T!T1a_Q]r,{G#1K#@DJtTWbKDhYs+d&f5$
e2r-x}$$E]x1jKb?*hC:U6#$nP!Yl_fhiNpqSQn,rFAH^Ej0WL6iVbFm)Dly<^b#KHc5d]rDfY=:)ji_M)GclRh68dd;^5K<_T5a@_N~m<-fPk)n,Yt2%c*H]%J|-hp$0S7[OX%#$N4]]QP3#il!.zdMS9b~OTUbf28N;.2e#?[uF^Zs-}.fOTD2kINN/_9i>)k^J;)<,p0Vjb7{tkV#@~3e%r)x]^Rga3?&.ot]hsKK
0RvY8Rcd$eg%SZw7upRx"vEUKc1n8!TO-X!gT-)*AjLt.nkxls<V_YmQicVt-sXf%[@j#pX#mg5h9(
>WFWD=eQShDcxKsdG*FxQ|on**[[%wssZ|,@a(y#A:>EurcPU7t,+8`@x02UbWotPkWM72h9$VBmG7q6Idd1z"B(
HVL8"i4&o!o;@W$S3NSF
!BELq[+mkj"L<1^->lc)Q6h?EbbC(-no]nk$(P8TBDBS#qatq6(vP68>:4v,f]68(fz%qby9w!D")/mB?*V6=xmTa%JY26w{H/DA0;97<0skOU_*7{taCF#q1&FBZ[^a&(jOS}tC&kDpe1T6J@z)gFAJr|[sNFt-OPIbHH>j62GOYJM]Oh,VmV(uW*f+C^.;Jx1(Ax^ue6[u$/O^15L%NPP|#[ukll8d_fwk[a;
fjC]c/-/7w&n');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!]b]`nsZ32#tW"t=D^f-cSgTKbHU
`OQ{E!:WmYA8@oVFA!5]8%:/.^",RA)Ub_b@nC";#1ls_vRz_}Mm@*^+?S
d)r*3/j:]J$kH:(B;r<jgc|?(dmI@U4)R3n2kL9UT1gEMGg]/WVrMy$/EqLC4/]M~)2pzyAUwk;F*6iAe=Z6}eCL%wk`d_Z,x
a3Dp_48d+,{nQpcZlcB>k2`oF!PH<_u`fkthnQm[G6(H+d_s37zD&)Wj&a/
Bt0uh54EUb@Zr[o):P]Fn
n[JJW(:>>g%@bjvV=7ZbycmJIvr)$.PLTrblmguSSx&2wrv*ScLO<`%=
Sc^(o+VLYb[l7L7Y[d
e
9x0UtD^Q9o}w_;[0S&p]GYqp98YN*XSAJk-`m8}Ja<e91
a]BTN[9]L:cDRf+BNloiH;si{,DA0rBj
UNXpRyi<n?M"D};Gk_m@-nv<GCm!_qO/
p()/cK<Y|UkyUS;4m^(LzJYmV[hMQyrSLskLVl_JupCd!34h;q4x"XaXut5ygpFSt]"+"fz^(;yyE)C5sb3BQei+:m"hjL1hq7Qu4p$u_=7/GdV12T_norT!9nVx$suQ0;G0K@tan[<mk:Yz%ooMsJ;)J%~MQe0y3Tri}/2XU4;*Uv<0,,e+Y+yEj7*)A.!pH%<M6U0Uk#:$6E57
DS7#)]=We<7$[yhZV<kF`Cx3[nmQ%s@|X#e3:<r5VG<~%sNnDA9(vjjfcNT@LLb*>"@?hk;<VH/^`!U6VX4NnkIUN!>}#6v^Zn`i7LsCjW,8hbmT,NdO0CCB^0-R]5*Mow9-GAv!)9Uc+[RqooDSs9Gn/-<8G4]QUA2`AL%,!|!HZet<,pk$k(rQaum?K_YP0NLelj?4ZtD}t_E]u/."4MM|s5S*fY.tgvVjyYegI+IYF!m]+oGsIecU;"1M7y=2x,Q^<EBDnQQJk:p
/T=C*G*g%i3|;:`73~dWY90x@dVS@:$S3h;$Cl,{sy^`Q7k,Nk3z:#tWUTB,q*3Z4q<7np
UPK)?=#,`9mANW%&`cOfDxT!?x&
*gWa892sog;MjK,gLh5#G4CSw5Q?-[r1-TmTGw4V?Ho!eXC)&ZEJ7F3RsoEm}W8uR]fLf37gRVv!]PQ0E"qN4e)V*pgu)"^z!s4uiIum:(sTX"cD-W)2/"%EVROBl-3*8ur6Yc|N=yoN79@"(svOEEQKLqpx[[!$rNPd+]4Rt:Dz"VTx8OhxcTG!hg*%;m,/5.?Z*pFN=)*b#7
9Ju,GqE>@&Du6qx66Z<2dei2mjcpy7;9n*t~u..k."Yf6r!<n0V"iE3T;-oZjQqS3i4Dd)`S/w6F:TZoBO#)D=IzG#MiNBHw,XH}(&-ltd)Q%Gj|UGa(sGJC=1?(faK+T[Tg<Ww)1w,k^j&utOIqp-s1M*nAq]Gn2IK7TZN%3ry0`b827p-YVg5/LdQY-*W<7Rx-:n*UM*8~X$/[5hnP6+x
U`w~aZ<yZbyC_s(a3f75"a,@EMIn6CVo8_,88?K6^exjh0/WtwKR@|1aOFfDgKPW5N*XhZHkF2$Dn%NStlp593m+`[WM&"r*ElnA6h5|kD1VIMaO>=g51`Xp.a$oLUB/2cs-&T/lq,`/Bm!|]-.SKsmE8
8VA0AZ*^C=?UX^D3$se(nfD
:IBR$`VYR%`lV!n{<1CZp<9Lqw:9(jIpN7A/I`$&?z@j#G3:Yr=]0h
EUM:|WoI5p
ZA,s)dx]#ai{x.g2@z%}(b&a-0
3l23J:E%d("5~`E1puIJ7y-Q#8zcI[]E3LA6fbFHBjNcv"KYYN,b0Tq4/BJ)q,lc24r.N
glI
t$>4p[*]tM3SChr_xpe`Lr.a8hP8ar[wDi<X*"=A*h0wtGK:hIjl]C%2*%_u3xO)20r+Ia^^,_JwpCdR+H_!C4q%=jTV
)R(dQV7@"cLg7LtufnQG6f&mcg+:gF(c"t3Cs*W<Nyws,O#Sv|VE8yG0hY]>REONb]F0BokTvuO4Z99$JdH4qmLx1OY
yx$A@K=oP/i8ljb;GbmO^CXao/J%fc,QJtle9G01]81hP?[>y+FP`NG^N1J.lPPJxYT%D[[%eSd.=W;E)y*$?Qjp5^-Xu.t<;},kf>p!`PR3s$Z1t^PopL=[JT@A)W
~YvT,ISw,M[j5"bJ<=eM=NxP)@4IP6}<g#@kdu_F4#CA&V|AD_~7BwY,.d?LHjB+`W0ht8&[-"b##NJ-V>>=7DTjn5?C+uq`i1+L<5
TIX]
m?r67.&nJ9zx`9Ne9N)s`,{VPuLpaM$%0``Z(e7xM734[X(C~T+Gktqn:fT?@Pc^eA7u;6;ez#W%O*U=5b8nr8UZEEM:ja!-
YxO/kprD#QwCNO69pt.@FJ5G"C!jAP8T4<[<v
o`wnrLpo3NdC`-hlt<!S;)?DJ%n~`xNV9DF66Q`rR+P|)j/Ijs@8/3rc%LVjII&".PTx9!KLX*/Zm=4L/)Y(0DThSUl_+]b_!%8}b/5/T-tEGPX&Vl24P
2"H(xb_HFn$!OVcN/1S%KFf7:3C%UR)v[uZ#[NDFE|B{]6_dQ)PTt9dU@i4`pm2bX4LiqWY)!%lje.K=7"OM1vx_V-KdAoINfWp*ZwO).3e1=g07(APZHU&hyECZXXqGoo&oaEA0emN!r
&^QW/<
K>]ub3&c/lbuqvWEj;iBIY
_pGBV:LK@"Qm"aor$MayBb4RS*F`Al;SvUsj1f[^B$%.5#=j]86*R~_nREK?=L:6JFjgT>jJ!u`Q(VU2rV%K_8".40T8kd"kxn(q*X;&"f-D5JD*g!Qzhp[Ud*<T
P[c<xjK"0jxM/&m:TR1N$d(`;N/mArY&ZCub!t/XVG;ZL0=Vq"/Wad~m*5w=r+`[+<Pr<b4*u?z^J;]FkP&sq!@SIL82ye`TDUPhZrk-*f4Qwbk:)9*R[_#1m1_)3o/<ENlMV44c4!)f~t(8XSr#el8:p_Ksbgx@oN3<Z)k@p@uAwAJ!L"B>+=%%!w&+I@[Z3eoFK=kgaM`Pc-UG~0*^jT(.h!tr_=a_NTLk/euq8=
iP>vx0M2*EDb=>lt:#:ad"EK@mK!&NXRQI>iDmgCLo-T</Wki%&91Ioe7
>i9{?[.T`5I<jj7#ufNOb/!C/C5Kt/j?.RJt+8(39JEn`(,-g_SSfuN=8#X`jve3)Drt6SO_[Nb{v!uv"vJr5RFrn>Kr4|&vi1xgwU87*3_9=
AA:b/jdjhvNUtvdP`6nLFiQL>df[v}4Vdwq{[L9Ta-NYaOrWAV!Le2`wG1rs*Tiel@$s!CkcJ)<DU}r"#QtG3`*Z
M"W>8Qu1S#j(xP|
PTj8~n/lfEPH,Vbxk9S%5EL!U_-J&V>,2b8V&2Z7dUH9Im1QZjMG0pGGRLLE
SCYC(KqGW$`G)bO{GeRwqrFhP;^{/uAZGwt[D_w{Ak@0:+Q}hX"1d_A]7/
dT/PV.-rN/QQ2Tv!6YAMiX
VW=h=Sg*)6n*$*]-E0s`P{,-v~CrwI$Fp]=[d%)WGB,!h1AJR(-c"ERDggxxeh_pL#Zd6Sg_:?90,CFyoN$(
xcVRp[+ALsx4lI}Ta:-5M9SSg5H/R!s7M+qyUILe_=P*J"b-P4w7R#KM]orS1
h5>ZjTbit
3j.UuOom3g?_X4Q-m&_<:eZUdG;C5D%8pi-2R[@6:GN`&
81mA6I1(]#*$5Gt9p.MVT:[iY]^5sRGOPJ!VbU?!Zp>gLKrmVB0uGT02`x!,bWy.sfg<Yl>gZ*[a}l?W>p&0%?:(RSf&.;Q1{ASUGQ}u+d
WR/6d_5]]NkzrWxtG.5
o9;Z.[k)_)eOg^v<1{EAY%&~Ya]&h-*U%c@P7(,Zl;M~5T=,c-/*-^bfRb
~`&j|*oRoi|
_?v>Wu_bv8Y=``7@N=bA|M*U4.Jjzf|45RR&w1!
!:r=tMp8OC_#v)EGM1Yw_n!ap$]A{`J+CRid(!Me5;@!9S=
UccJg
?Kq
42dq%_f_.u;S}D|%DL6m`;C^yVAS@c,nmy4tYk~Av-<nB$~uG3co#vu&;pd&KQ`PEE1>0n+OI?&,M=&++m
KN-FmZfhL+E8U2YL`VAFth^d4Mv0r
l&f6E]$4Y@
ue&b7?0ZbLIOEEE[BkMhkHU#c@q5aXBMb_&4LdRH3(#3?H+Xs&)xqc{0i%q3sU7<z@m%jE^S|U^-*l&EJ[G,BphbX2&$jKtF)Gy&A[4DnbZe2iUS5-)+eT2Bj-+bD:frDEDjRqD,$#AN2VBAfWxd4-M?gaf?G:l*.oZ`A.,@8t;eH%2ReQtnUWmcyIyVxl7r^2l.U@D4)pB.H21W#PmDid:>"@h-pPxva#p[{WK/_.PWS/#:c$8BDna![8e_l*Nd;[d08:Gmt1A=Q<#VHcZftj.s{$b9HN($X:K,PX2]X0).DYx81:Q@mVeuTDS9O^}Zj^|_Gg=:=3`P*FaK{+aE%J6Ea#7H.wwc_.;
S#)QiqL$(^LnFH&@R<&)>ChD.Luiw]5E,15[fDfC"j*N|vDqMnKgVy9eV?AiNsg#%H^0U_fZLh,iKb78zdDb6;YXxQ!s[fX0ec9f{y_m.KwL<$
-Wi!tQjfy}5Yd52tq+4.$
LIY1])3s+v6`XY57Mx<J8F]LTD^7xOyi<hxp7r`#Hh(tV]DP_nq*+xYQs*th;bmdY[CjAqW1H`WNU.YU>AxvC!e:-Lxl@ppW$eZ}4`[m;2/?W3TTZ%QRtOu(`L4Lu8tA$LD&Fc;8[`(Fgd>1_Mr
]gkESP8D2c.y9Seg;FS+Y]2)"Dw81:pPey_
K-%+X5:DEgJ%g:N_o-auA<UvH.AD^e8ppd+*OfByyf
;_4"gLicsEj/R#=h>ZTUS;%B$[!h#"y^BPa)m#jE)P~l=hboMBuBguuv;c{C2im"Y_`=ix6F&[a3k[fT+4@@sCjDOF6g5r$G+E&a|[:tC1;5VG=tx3LokL;&#W&,%d0oA)H?}xnR@M<9R11a7sD,@,^lMucPhT}kWBzC#:q7|&Q$_QVuWa{Bq%XVe_pn&B{7/5wA9+~vqmpGZNSQZ,^g(OrCJKi9
Pycb9A!^+2e+JTbLQm,K5jI:/WlZo))M+S%57IQaVcB==|A,a4Mew|m0"WAe
@SK!_rk@JGMUrP*i167A?)XgEw]CD[^H;gW`vN9r,,CMQ/&D#%Vws++BOL5$-ux%9Gs?/ASCa=X-Wx6hgHT8;F&F:FX($rFqDK{;3+"F5(XEPk_#t)42F]%l3LP6@KJ
:pR%fJED-NPV,WFE=q^0|_9K;%tA00h
~HJr7pK=`4Q4Y_5F,"<_Wk=6<:@FJ#P$DgLIp_7f(&W
ux"ye
_E"29*^SBb}L)^`bA
CvEbSL/[nFkwENK,gN6cMS@c
u{#hdr5*ZS`).+2_(?V}BoW_e):gs42ifov!r4kY/P)yN4L*,Og3Of"4pht2bB#^@>OtWn0-Df%xtTm}W$soifY]6/O_0w.9=jw6sW@V++2y+fb?O}I8`8u&7kx_TJ*Lv]j<P<?6sJNg3p:>9$xdp6#GL?u=TO^{,jmK.f";f$J%tHXpt"Nbcn#NPagM@cupRFfT<]+-*{GI&*kP4=5YN~<<Hye_d65<@a2?JG/tWK4m+]0w;f46@cgF&5P)Ao6g0{Rk;f`q.V/e@oKf
/NDtjw;Qi2mM<CO/+Hz5+N^sa7;BSVO"RByUBR}5XOF[46K8hCQ@02`Hn"]7Vvwl2%wmW1wcSycJ6Zy@h+W@2AwR3y[h0tx]_AQGf/CXYP5Qe<&JZD&-":0N)=c%4alPl6`;mx*FN>Ptq0p;g1Y_EF!0+C,V8)g_:>!@"=)UpX3gvMWG&jPfY`n&+vP>U4aw+1t
/S`k-8BW<h<SI`[(`Xd;jRdG;
^?!(uqqn@<<]Ru^A:j2fb7p$ghuu94ZFp?M&TDuJOY,DU`-3<2/,z/KR%0];3Sw`%<XbgATX>,&jLFy;4H29GhZar;:3K4`W85[j^u:@b*>IM)
9o&C7AL@oxp9S8EA"g.>>MT3Fh5|TY24(
f5&9)A2Q4gEqTnb#Ow
WKB3cj8ff?[h/CvL2I$Jx4+h_4RXE>q#@qbCcf-?GdCjE5;$8PB&FJ%jpUW]p-Q(@15K^<[Fzpm[#2QD>D&Qz3yA:ALG*TX0ux1b:[d]wUy2@jc#JU6%eQt[h$Iin=;RVq7nlOJa(aG5)9?63:&9*x2SO[M(G^xc,8Mwo3V)/BRd2X%R~7&9|Ctx,yNLSa~C/a#v=!2ofvn-4av;^F(FEj%Sf]#YVN:N5(@krJfKw]/!q/JDp[9;;Y
gtui:BLAQR@G_AYy0{LbC6792~,?TyS]9*WUN:!:VxG{x*=SeiW?H
Z&j4na?<bLe-Wj]$F}Zw-^&[0z/1b@n"]
x(%ok_Ea]=gI<^2TYPwo9?+ft;Lc
^gjc;cnmx9}1^_`SZ9jlTBP9hu`01-/RE`U
)FRJ(r%hvlxR/OowL+`4}0Rnda<p#Ul>~s31[]YJvp0os$`D!OZlwy^/x@oOd*66dZaSJ//_m*_=xYwsjj:,rjdb.sNrTR-guj!(CI.^<V_+<=s`L9/x,S_5]K,*kAKp"8.RF?2[N<&l6BKkmRQDkvcV-j-J?"G&@crL6N|b4D;pv&IVtVD&fS;2~m(h+#{:|Kp5=ZwEu!^Txl~.B(.1&o5$FM[ROSvGs/ZCp%7P>3VxE
*#$9zv/JX++(id-@&iEI39LXbR$8U
zl`41IbIO
b%p8Bp$NWj!_hW|OqP3ghx*Px*LGH_z=NBWn7HIa0o6F~i$Ho#6"E4Ri@&i6ziQOGi4U.^TdsAwm3Hg2:jrp4%1#YMgW0?Ev)CS"BXa
Bi$+?L,@H)Hfy(nenu+Dz69Nq0
M[?$$fuZCJ+>3?im9eMW_%_#yZxTM?7vD}%[0P_M$
?V`>4s=^Zs?D^aB
:
aqRw%>bAvmQK7[-^.}o|;nO7WaY`Ugq2G)K`hd10qNJv_@pR[%R+55I!@k`s"_UvcBl5H{?/>^DlB*nO:XY`pC!H
$#aj![0u1ynw)?(wLQdvmMHx4BMYwjnHa8`5%r9<&O
)~3h4JQ}?zCQof`J)rRa^6=j2T4(&CV593F^#YsWx$Z/4Kp4^"C4&jPNkS-5*%2--w7QqqDh!Sgdu.]E5)AA_NhHd>F96TZM$_0oIHK{Jo$J^sp^yx@)k)t1Y2%_u#WpfN#H*f^;)PE;vJ@s^FY|)j")L^]`NgAiJ2a>,_afEiahb<L%[uNVrC1gW($/6$P=+4!zq8IkV;CX_=I.7e?R:!CJQ*$,/J4Xd58y_e;Ro%7!Q2)mw
e":nc5v%Qf`Z?Xfd:Sv`G!Wp:+youu6zP-=hm.kGi%_3:a82`[t-nD-B8%4c=SKCTxmmaHBs.zSOr<Mx#rBq7}
bN1h;TM_|k?aN)Ou2JzMsy`s?Ip)KBiJxvTL@Yi0Lh|Z[fn_0<D@3!A]*7*bmEl=jFJJD/iM=ET9,YOxR2i$F#"4G"-kgLf._e]FsL`J4I6n#p~5
fLgRD%N}$8pHl0*!MjWBcMqe!~
gPwTaL{C~TS%Bja3]N
?UjI7{dn&!Y3L;k/k"*NxqZo(HR@4]EO0QYsUt(,!|XC&e0;N7;Vx|)<1.F;i%nRS{bg9/$3#~jRU}8+/9w./kRqOr>9"=7A_?p0ThPrX=!a"Q7m2$T$,6O62QAAU$WD]7kCUPUQGyM}>`f>r>d28GWe0[8tLayzVZe7!OyI+xRBp{BRCj1_I~UVL^2J0*Z`=u7r7]H(=dkR&0x6Y|B&[3
B";<jS2!3-skW]y$AB-Ls:G)XOZU2QYJkxW;KWV
&$?Ex)Pt#4fqZ
2EA0s6{@8Mf8-+26nk5Jd*~jnp_Wk5+F{%NJ6SmI3e(a1BC+s_o2P<CjVU06sRH8UPShlmphPI%.[7
1DnKG11Id[&~cwWQ:^^#xBq[uRk^%H6FL
LfS&,;G{rxQ}XxQ6r)b/I};c(J!5l9cog}j?pOo/[.82y+Qn;Yn7HT1.hj*6ID[!n|W%hW#(6je0ab$UW+33_3f@y)L)t7V7?vgJ%x]$nDLq`;u@WCa+mHm$JTn}V3pHuaLv7e)o9$Z6"sdN3+4^4$,.YvsYZu1i7@
R0k;g71^|6Kf@4x7tY_U9T$][dACxrO
JPZtt^gMPLe:geKfw<5MoKjw2`LAIcb@tJu&LH7R5Ys<k%*F]x,Uw<t*Akqipi!sids/#:.+l3MK<1Qoj8/E@0qQ_lntb<;sFEf>!Y~fL=Msb*mDCn1r!G^auK6UwtI14#[RJ_npL+0d%4%>T&KD}5FoL>M?9@ti^/S1$`<s*<`h^
BngN
2dj8A<)D[?WI?F.>g-X&qVR%jN&yfcuhy%spuruvz!0za[H>!M7W97__
yN8&tfg7xvB*|JNDY8/y=NvK?X8p1RvBQ&Iv6D{"#"qmDV_EjoAb>1}x{K#ceqo0L=lr<Jp^"LLFDs")rXB#1sfB5/uEE#,%jukAK],hv&=<4f&/P(^amoiFSABIMiWyi-7.d=b"HIcKU?!R3!Qt$p0S2YwbV3~Fa4vqiw(^x:}"LA>a8[+Z_,oON#v],8&_p]FJ5l#%_&NT!`%C0eAs5;CImn;HxJi4&ZQ*:O;L5n!E6LwGx(+eq(hTe:d?n$89#5@uHuy:hmKQMDA(TjO)&dJYQQfM-WV1P)^gx6r0Ve1crPEe0-pR%n/@dQ~F>Aiq6?n>)/>E!E*&NBYGhf
o7BCCqv?:?>L9_4{$0hTD9OcT$)`U}X55QTG,$ddm$gL*zZ_9gy`iP4Ae0L`r4GMqIm*@Rstv/$}jj%?#5=HJtNF_iYS[[f;=-g-?U=(LvPSS[xWE8V~cUgpSrkJO]xU"SWAB"$sI7rUTc1"<@*6#n=iru/5u,$W/:#"wW!nd+DwY?saG+f}P_s<-dOi
UHzlVZkEu9`QVtFd57ZkW=J*m0`D4@GD^gJHiPbLHJ;*YO^]Y.r<,VkCOGt7C>>7"k;ey+I9pEESke6$S*gPepmldJq!`<P=vCbQGf*w:lib,4i=aSy&E"VcAG.73G&]`KAr[X>&c4k,}2(QLxhM/&gl$5BeNV>nl)N&9&pAw`%qMws(U?{/vwA^zTZX`XySt.<M6g]^0&xOd=Ok6>|36dQO&ePDKYdU9;12F;!?**z?;Id^c74u;ZfqSn,7V*lm<ULY:S
"VL0f)NH3%XMos#Fb5K*dNg{mFAI:gagdH6Pd:(7]ln`-5)B2ZV<`RU*JiiWq[4wx,>^DTl`Y5TKhQ,^Gdk8Y
%)V0SDB1NEcb8#d%$6^b.GbZg.8tM+pS!5v+9#;Glqxvk?D>)c3o3(-OLp"
3_(BDJ:}gs[@o1IhXhF`iF
o>1_LK+U
%J5m-p.BbAKj`0%23=QoFf`yD[1#VJ/y5`K$>P"E144"qY2fM#JvZXd[WsjG+mt&K$/.8KnC?d]h6HFk3o#QR[rv@)&sX^tD-MRd`~VkR[*I<Ko@,H>s_wrArH8|i2TTd;U9h#u{k=#sT0y7bURXI-/I&#Cfg+.+Bd-*W@h#h>_`t4fU[kCkeOL<l$?w^RExGW/=f~/J,r=%YE:J-HA7pqFb1_/u<$+.]%+)=(`Tf}E`:@
qf}a^badolO`Lwh-uf0$
5[u_/7`(K$<UYHw*g5+Mm.(F<H%o6et%sEe6J*P@)G[WCR5fr/(D[!"#
xN/X[>ghoX8(<+M"WhQ3GwUs_]o-Gn0ZNjFvpN;*v3#&3KBI-sdP*Ls&sPKm3<~oyK9rBgu;1XFv2Q%0OmUQiG3r(^H_]dI,3+7*~!rSx,Ul?CnnDa4.U7mviQ9oj_|;cdq(l6{_bYg3g8b!Rb<-|Lor4E*1(jKUqcNQbq9KkA+key9k{fzw:S2QZqGWc?7MXsX`3$vcEPtkZ]"!R"rQmbpO$k5%GL[z$Tumfo;fA&
Fsj1;"K-(C4C(sD#r_ud?ru5+YJ^GCQVm4rlg}!>T>?]^UDl-fY#3(<h+=TG
~4l%kEq(@$5=@E|H^IVYK#;99Z4QEN+GH#;=Db8]^8]Yf/q<Z:@Y=>4gwAm_{5|Kpx8m%bS_Jtdlbf0u&9%9}(1Z?pm0
Q@k}c_-NkUAK$s-zNxBD/CLkC=rj0-gm!CAyjEa
J~d5Nz9a%f@NlwDnF*Br/O*Yt,.hc%-.;T]g0l:la-vv;ksTg7?3.]&X
.Upr[QM4LfWmzLD;Qswvie+`sR4]AJr)tq6gq>)5[P=>j1>2|g64A(YqtVWhVGARY)K_7Zn50B77]idHYQY:RB9h;7HEdL!>~q}9j5Ad-Crih`c.F2K&h%/[U^MY.(tt=m0kgHEW|6%OaqMsU[NF~`|Mn/fB7]v[r?O?&mxg73co|jNx!@q@Uc{_EH)3&A.T!Ww3Z/rjLfqTZ;zmPm|*s^/]n[{UixmvrpX438UXg%1ZNEk`b(k[6k"QO[NgOF.<+w:&oYYcIkdF1MRk(eIWl!*)mcVC.^kW?4,qd.Pi7Ki^1#s^*K.k]vYYi<xZa@eDxrajuxI#|1*Lx#*Vs^zc,.D1>a4Oic_)TYx#:tj^jZ6HGaG3(.Ig#4(G"DzI$T:%hVs6n(8*Z$|ks)G@a
p6SSjrL!P
7dnKxni&`=}G(Ta.DRId&-wS
m264u%pI7E#;6zaO=^Wf-;"7)fi9L!MHr{sK7MW_5e.(HF84apfaPc)EwptjNcK6M2S/#BKd/209?)DwG0$}R
Ui[0jQrS=IVtVGP_F&tL^q!}CV&;QPdSG>,v:r(M^[.P:*kRv;I)?
L!;,d[w1_m6&p}3lmzFgK(0EdG_0%S
4EZ(ndr3HOq`i;x0O/j:dJwHW`c$K<PT!
f6Qxs_OI(oPe3Y_L)khbx@O.0qrhhbLU63Yz#t4Uo.KT`<|Z192#"G2j7
}Js[-@%yN6pL4;#@12DJvrx68a.nLa$$CAsS~FrvtRE.69FZ]Z{MbS/<:Rte8rqR.[N>^1RDSQ(&+2rTqyckM(`HYG2dEbqMbhcqfFjct^Mhg%ymP0-So%f&4FvA^a;K@@n9`]nlu
JTG]3JQ/""A
WtT^j
z=_yx^E?5V2a2fYDXo(hfx8]X]C9i[2Y`o~r2bm&XQYFx)ekNx)l|2WeViJ>O-}MI`P>;sk#)6XaZ2Lsz
2SMX(-XX+wN7*muu%ND+En92H,~]:R}1?2cimh-4h9w%BJXAIalxj1`q*p+XY1y;HTl&fXp"eIUVP^DfGl_:"@m*/t#fn!3Ga
R@![/k`V}O784*3(~>nPsdwjjhY]tydoswjX%]r
}-tO_+SntrI>3[Qk[,/R2c]m8lItMv(4,s;-ze%lEpMXe=`>0UF<<bx=J?Dx$qIK!WAGI&M01nsm939>uTmfCH)nh.keYiptR,{=d:U*W<5x.C/QOYfY,O3@V0bAZ&2cdNt)_xf5I=o=$-=Q)[w+A"T%fqO?9!CC).;#]@:e.f#={>T`^m/8*yJjN8`c$-S
86Ok&=vpfK]U9[]2-ewMf$#bIW_3Zdiri]AR}auvYco7KaE=q==I/vnyQjA2:JonFMm0!QO_y`3Yue]Zq.B/Ox6bR>mZWR_*78>M/Wcm?:J`%L<,d"!YRs=(jB`p31MG{rwUzPvd]hA5fXc]6:[0
(~w
wZ1,a+<g<5F$7wq+AN?("&_bSo.NR
9hT,QJL/9!yFIOcRJ;I~YVBihywvu&&~Fl/n_&so3p?NW>a$<.6n,qsKI([k7Ge3=Nayr[ctpMwnu"RhGXb{;&m/+#kzht+sih`w42F1XNOX/32z<kP7y
#Qh<;c!PRCO0#T/$0%FMUg>LARsFUCNn6Y^"@;5-v$e]g)1&;0:kDaXYYErWjYJEMN[f_N0/@rSM_^`a4B7%N&"xL/)"`)GHa2sYGGoC?pA58RD+vr8gr!>02wx~FG$1<.Lz4C3Q,cXMkFt!4md<E?x
s#FP_fMpy"_?*rDd2@.4k1#=IJUuk[?E4myz_M/wx_j+)Z(~QFD{-J:.ch)rFh<N#J`Un~7|>M)QQ_wZMu"J!lMY"/w@IJaAey>BPFe:Ff*dMY09fck
8!;qVdecULvxlOwQ#5<>_NHt4;c@oCA2lod9nqq:]-vGE/xU7fj<
HVd6:_GAx>!#X,l%pSz1gby;K2_cLP;#$BRjD3*HB&^:afTmqECAe#/M)
u7<!Ixp7?r:pf<l<n6-J[@~rn,e2xr8P,5^0*84DLQ@OSWNb}-a?{hx(;Dm*uj0knEJ&axyq).?y(rM+`yI%75>!$
j<MY[hNK.y}U$2&Sq>1Eta*pB>gwz;.?+7LkDZ<OrJ(P|Mw!W4EwCQNW*ECd+fX<@]
YMY+Rt2RTEu<!Kx5#ry~V,UR)/f9d:*PoDX~e1V9e-+{PJOI3wXZUP6b#;t)P7%]8sig7T4Gqc:docRqa$c#uz
Pu5Xxr|y@hqt%chw13}[m4e9V2"B)f|)V3j$MrpI>TBl-9DOg^(N[q&m
hW--,OTuM5<Jb"dSgB2MwmAu<b9hS1SB)wvWi~8DSLtCj9`=V>%dox.4SVmbHGb
3-$YRAQ~[&txB4(GTp,bd.b9+ju(p.lJC|
;]6>-Gj^SP,lKWKnY9FB2hn%`#E(XSFO5/u3p5B2GI#N8At/a&bF[Y]Fh*4r3gwHBZT2x:MXKQcZ3?Y":nXX~tBqI]$B/[l55X}d,-A>NN;P|jNE{=SFa6j`B=xJr]s:SF.VK
mJX>A$}O5N"Yekb*}a&G>iSsUyfD5XI]4lE9t<87Z
xqd5LFFTvS|Y1J<+}k9JDh3^%
L;)WJE@yVIoNMCVc|Xq%%<~sWVgEKJkO1O+uR7oyIMU4<cD#:Mjh:(02-uxy[CgZ}d[36%I)zgO7GMmDVl5nW5WP0$Gg<QGig2,c(ooIU<)J=Ovs4FxLd-j,i8,)nnJ)d^#U^,1=GshFvK)5h&h!3kd(X*|qEjqgh#9w#U]x5<RoXYDa7SO?E2<oA*j[EJ9QY@SP]iJ
(V5WaakAZFijXpnVc^pn5)XOr[(H|%r8!6D@/0C@~5Bh-_Z&QYfe,TXL^MaB:([w&sfGMBuwP&swv<yo}nxmvYM3beL,Y_TGM({;%<hRG/}xz[@3rQ=:T#bPE>K$EmbvoIVpOS5v&Dn7G#<#1o1U7-d>6/WdV=#jiHr+v,Em]@9vwe2S"AxvV5rQQOwh}+nn&gJ?Giya)q%$_>A[!,E={Ri0YFbx6flya?<F{i733$BcEGDIx,OAoh:V2Fy"dJ<#mg#FskCg$EEWl@QLUag!yJw.K,!-nVAsB[@0
Jara1wL"2w30W!r-X8
E>/:MGJ[g-mq#o`iG)sN4Vr0pEyp"?y`s$6Qc8(h%)Nb=eYRXrKP"*>e"]LO<64]yVOb@.A4Bx<WPZ1Dgx36=pWF*ESh_woVjMbd)6%@i&EUNN/C-@mVSlA4P+0Xsp$i*r%?Tg@#MqA*|V@J=ju<Sxv:^2b9Y>TZx@leeOzFuXa<:EB.kC4K}gx9r(*bt@nPxe:]c6=j!1eS

:uL`BOSihCw`@?y"YCl[D.U,:;{y/4`gh"3_hw(tAlk+QycvvMkp*<32YOJv*.[_G)gv~l[:fZm=V#EaRMI!w+V`CuwYMyg#!OvX0![D4R4/!D)-/3U!XTj@M50XB](!OEl/we,npx-m<)ehkpyaWM2vC6F?#Mj-9T@
w%/obM*hioe%LCR8-mICksEG,Po<L;Ypy99qxb_bwc4kzX$9@3Y$u2$;!:VEOYlDb5
]
_$WS=7:EED^sdxkW#fPLaw9D$qt(`Odw$0wtPC(%,)wd.2mo]:vMmFM5Yr9)PP>sa3=&h|M`"p]KyAE[ED+`r2yZOG0g$+)BY".ASVtK_BpyIUX+[Z-K1("yk:Sr-<5Up/P+BC7,#Mj~"YO]b+tqX6E4P{plm)hRe+5vIj
~0?MyV3OvAV9*V]fH#7ud:UGSESeu^_#[h4lXAaeFmj)@tLOXNr%WPxUgp9pp9{d-dN"&)z&Zi=bV^v:`W-sIO3&nsDim?NdI-:h!!7/1r^NAI+4n.C/R%Chj!g_{5G[!sD0^=%CFC_s!45i~Np6jC22h;vF-;.:&9mr#wD=6bcL.]XK}U7+No_8[5/kf1=_8)#14u.?I[e(WJif0L3"%j;cjNpdoWFA6Hi;&4##.K.)lm|71CIG"Q9.9F(c5=(qU(>"uT^jgSctt6r%<RwBz#n^D`=X-#Q1(5Td1m|sLbfRN
rFU?1
=;8/*fV.qqC4:&yP{P`7HfWt(7QVFRT*<hwqg[He""!Ke!N-G-|p-1VR}-EVjCN
]N2x~0Ew`o5aRnA2Ur/pO<ltsX&7[R7y@D"_4eo"nLtdP:
p9$.=;M*v/XlQqIII;"1L,+zq0rL,FdK[j>k:kVG?jE{L4.Y
3Hg8|U,xX0i79yC&mk$ER=Ysi$A8lsC^jb^VbS1Gq:oObBy,`R=&iG$*dm!sa1$nn&UNK>Vd4vWnO(JK"L{stC4@sdY5|D7He@5QeKsO7l^R7+|Wni#,%x1?Xu+F_Blq+Pkoh$<US$/n=Ze245L,?Xya.y6lq:9r<$q9=Y&,`');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(',hk^CG^ZE.5v]nf>]Jce[)I4
vksP"e$sSk._%<(:+l$J@n;gd9=*1
;0,cpiiV]^
gkO1//h0It!.BFd@7w/^+kWb9?hNMRQMN.bo&iSEM2}2i%?W8rOlkwT]kbsGLOzB_[fz%JgXpu"Fn_,RqnWpi]7HD_pQrbYcTt58Bxr]ylV
hw-(HS/8c=Khzj(BVbN<`Mjk:A_ss<!xZW`;k1L1(Ir9KyQg[Shv.upYdnot3mw7&Wp*3y2xmr
uXH.Q[21RT*5I4L_:jMrTfw~)8)?1FmQ
>&tSJ#!e&.hHzCQXaX7vI#z+<jDTt&@Mgw-knFa%K,y1HBJjAkZ,APu!y?)(H2&7vXsuHL3udHxV3GQ$7(r?o/*KBA&Ph3[*O(-OTxVe~OW-vA9w0GQy2>!B[TS<h%gmAt!k&ho*,uynwwD^OHg,6cx;<"y4ypcFr6iF]Fo6zv#7,6[4yt2S=y6Z"a0qxe@M:EG#U1d=OKQu!yWVeEzkiEB_g*3r!%Hax&0)@Ag]MX_eDV~ravc;~JS%CFcU2n0[AT?3sz%xryChU[zTuyB4gUJe=Z$KKH4lEkpR2B-UNE-CO49L~B)d^AD5Np?J<FBT"h)72ujiGBbvVDf`(I.8Y$^0?PdJAdZ:<SGCwj2ANT{QO>-BJ)AJ;tAEA8OQyXpY?,URlSG)"wPU,9Z-.n9`ZWJ&Ycs0ErUs@e%PZMz7JE2>RM_n9H;W
*kk78v!.OiNOnZa&*$0M0jsi)^]_90/qU]x"c0W:mCx~W^R/:fGfnih(U`OuW"M
yoGI`m#&MJ=="M4O_1u@65=vO
H:[cq"EvT:mJ#qO85UHt(4j@xkR()&]!JB4)LBcc]^q`sp1<)l:H9]2}J/_#Tb&Os%<r6ArH4MZ%1mw/]>qAft,aNl(PeH3vZm3dAdpvkpgYQ5Xi
i!u*~I^WOF0rpkQ?QraMYXf;]ktm!RlyWZ6m|g.=`GR<!44I#:0*>[#<p.O?^U.2.9!4D"#7rD%2GMXmMZuk|Ds>ulilzozTJS0FkV::nMhe.<c&#Lv?#w*)x=<unGgQtJBH9`MH4,c$#8l2a_o5&6<wnfr)`Zap7Z<]3tRnIu7jdp31W,_ZjnJ@7P1L;+DGp1Dux2-wzL[XksnVh];"0n)SajnY!W1hwxcKx4eUplEXSe^Pw?hp:bS&3SOb.6?`5cfmnti&J:+u
VXNET7<DiiG`bKyqcX3-F-fC.?C;LTu1bBD2RC`=Zq#dFYudW{j8bj]
s5w:AmoBeR
$Mct12GrWvs7TC+QNf7^_?9??oMyB@6
:t.0*Qfxbc"t%u&L!AXkBa5LmCRhhe1)VSo@K"rT;5%[sL4+F)wV~ohMdk:/e(f[:iPk,qd<xXTOuZf`JP"fk8sfAe9q^eY7Bn@<BvJ)
M-gEy44zCL>Ji""1/.*I!.k*04<ehJLG-MJ:J5c~^35rXz&Z6R>BIt&Fo24q#B`Eqzsv_<A!,;UT>q!)*f*J4,]}vb/R5=y-D#:9d"PDp(UyGA:qY
EeoV:OTm9L0f=#GvD6bsL_4_xFp;uyRky)^"]MkBU1
92|,&09L/@X*hQ
&4G_UJh2PR^U93az]=aPAEjL3e%B>L2xR!3vv9s5B)EcMe
n.dBAXw!Cm^@B7^]$+Fj=nI9r$ltEYzvWx;Pd)>2bqCLII}jl)G<z
^M.QdMg$6V+=(hKdo4WSn],z$<Nb*k),|E*w]RI[q
2e%vQI%M~V)Mp2`Kb)NMX&c:Zrh8NnXkuY
FdVc;m]r`F(itETcAI
5tP<tNqE,e`4=gVa-Nt</`D<D:!L)uQF#e9w,!wUUyqSHc
`5N$MOntFE4?P^BVR1B(.>#0`"n,+.%Sei`9M++2+WX3)n/zj:Z6bZnE)"[:^i`:hX;69v#d%a0c=ub=W_cC2LV9JP(+*!<D@iv?0!=6bE]gUuN$/"qZjrt0K@=O3X
<B>h~e**/4+%*@)_v`>m%h6if)a4C6|()p+E/VAd5RiCZq@hH$d@3lIt]
O`(O@h3cA3t*84yMEbt,<#/fNp;<?PqUKI%X+=PsRRFccl$21e8VQRPex6s<51cVh]djS4Z5q+/@pn?BwQt5b&KE29JsU?ujjsv1A:ZMKv8
,LdF,O~E15:EZ?hrIHgY(C~FC<%tBdWd%L?
zV8WMA4w<]jrnesY;<Ty9oOJoW0!;B5[MD}UjihK:Hc<8yA)"K{?7)Y5q/
Bc
IGnuZ9%"LUZCvt=C/q{>Obz>=6h:Aoc5Z?`E]2oSUMnIMnK^ye7%1Nwdq)oN}.nx;cd+wA:dD9"IaC-%_n:69S]wY5x=P

OpFYK:5.HA]p(ZC
`t>QZv,m#]MML!6-5%C
Gw,HaOELbTDI+o!7jI81Lnhc.PvY+i#C*PIRMF"N
q8$:bxpi=${%5?6j7ofGv(4H;uKErLFVW`%gBadL
,J?k]"YlPWtJ&=dd
7LjBrhDNv=e8p
SSh=%tVbXF4rrgflutxHGb,%xUG9>*xE8`M,-O~:zAR.ap#:sYsN,d-cRw=6U`m=5;aQ[5*,-WP?xX0u(q>2;0aSVU7^
k3lrXj`"TcuX^UTB"gP~5PEjG0BGtpI8yFiZoym&=|xtvo!Q+UG+Xr8(
D4
)HK~Tj,yH-^5vu*lh87dw:2{cdKM-P5z47$
61dRS>.@4xv1eOP)lEy"l19:8zA`=igEg?^J_qL{-M;Ke;)<r?Rnyx4rJmX#98ml^E7!CVcDe@`kqeBqQER8QSV*PY)xj~u)ZIMP2B/yW,9jwI6+
!y8<"tJpJK|wqkBxtQ^b?XOrTj-Ki^Uge]5WCvejf>_3?+jXat@Z<!-?_[+YQ`5=ed%c.b?
CRzS?Xp@9Cv,zec-&qjya9PnfXGol9MxIr}z)H*6QW?W_z#T@0{:Y?O`%M$spZXO^Up];vf6"lvhFQB&k.8;3f`PA[.n``Yj1*2$~)]xh`-O#Y2$o/Il*ob*SE>u@7ayXxoa~H-[#oVt~/v[~-6CP2L?U6X$ME1mHhY8lN?y"6;qCFeGHZcl~?$L`ocD;/BN8emF?&>hA]v";ltZh9cwI?TsF.vK9A-f<S3]^8SH,BbYK5N1)@c?]Jo&-*yjQq.=wdMXCqGsSvu+pDo(.&"xfE{/]!k;W`!"EwPVhl~K}agB(ss%MTF,Z5CyTgJF^_+:|!]Zrg"1&/-8:xxfNI0+KFU4#yF+Mt>JtMAyG*-Y)T#euR:&gjOT^kx,F+/=<2r
`b:y>y;KO!A<ma-Z10A:c?}5iV{jXtX4%dWM/(A-,W:8qvQRUm~B4,S#(JQBPi9qn<0ewonNit#-I=v5_Q>D!-j4
DujdfbMNk8Qb[ge0i>M8RtklUL$LC?$((e[8iP*<$
jQR|H40$e,Y&jQnd3VJZ#FWFRP^Fl;.5fMq36AiuhPZ)1o=Vy<LbEmE5p<7H=P-4=MZBWXl-.]r9EV)UxBo)!$$NZHwEU9z(ya
8rf1|-8L84JPvMlBz?)i`;8kV:ILt%RQoh:I~7S.)KZ;LZ8`lI}IL15rs8GSj7gr3(i`H2/tcbAmzUD?/,iK6W#.1G5.$Kp+#pkOnAssq:a`6*bE"KyL^Q1H]3masV(cdA+&|i&pQ.-<%-x!Pm!a|X>M]d`NJKPNGuY?(nXu-mV(/Z[6#l>:zI8_,4zD
aA5y<*Us(-/OQ]-QOjKgA)=/cH"y
AH>CgkpU=oC?vDW
VAYYz+3Yai:`d2TDr#_l2*DoFmT("$oE&n$d)#dL&.deDK
J7KyH3h%@PZWwmYv4n;84Eail%0NdOR>-~^6*&U(H;FsMs9=bRSj#;8a19lF>]g8m?2`6:P|lp(2HR-#<zqtyR?J=a/.Fgf.y,pDAeC4V#xjgx^4DTSSr]9}0|`:?/0F-[D[2faKO]x:"^c8=|I*X6yBVEESSf?[0W:j_<`5Rb<FZ(Vp7M&bZwb%H>2knA=?2>eXn9yB_mVbf.0Bgt3s4`x/]W_.5r8pk.N:ZAT}n|0%n#/"a|qZy1ZA#H#,p9><Za-n9UG`@j()PgYvSdVwy2F6yaI7?~2c#XoJ>DSKVDy<)D1TgwB!<JeV`cV+UKZnfJO!$n>e".*%5F`J44k{>h2rf
eKQ#PcncT1HKdoUjRMQI"!BO!@YqgQmAB9i,7JI9x`_gJi`D
m!TSsAtAUO@aq8

C7KLU`!pi;[GWdNrpXp$v-[sQBHC>(]#N"XnHRXXfS7[hDpQqG%
Gb
Ce:a*3`,Vjw`:wb-5mB&gwkC3*t!:8"4Ti3=Z,0RJ;7tf+@`?p)9c;27q)EfDl2IhzS#0L=KDDZ|C"L4xLM5TYAKF_vI>0>|v+iK8c9H1mCWJ(9l;gNvqN$"L227-)so9&Ve/yu0]<J&,{J58q+ml>3,]OOMy1D|3f@1SO9]3LLg-(wQNlGkgse?t*?2+LTUux[,"d-44q[uU[$!v(wH9AX_[~@{6O^c@e+wOI7Z$1KgbZY9Yu
?&LB5UFYa3VGWz(JFL>uqe_4t1X0US3dVm7cKD2K"Y9THN[+V5gRO)1k!m>Z,&w5;UaC#T]&P)3CK0oKocWxiQ1!z!2&r<.TvpB)x*nHPD
E{#!cxJ&-aUD`hYH)EIqK;1~eDJNN+N1qGka`#CMS%`lQ=E{`?NKS_6ify`u9)=Z2|p6*4y:le0|&
#i;<j]e-#F,FYjBww:."Lj)N
glpCR/0^Sz$<jND
tVX*7n!r*={dVQqdF<4jh;x-Dv-:c:}ZijGX(,7].t|kT0^tR(dy-QsDi%4+MvmuymOW4Mz4)5^#<`y7HC3v7dK&cokl!_FLj_gbqqT3GFZ7Fq3pUvl>*AXQK;EVyj-r6w*yLy[cW<!E"vcKY?>o/Ri(:,6%%ICYiu&HVG=sQFmt:a-M2Pgb
gB60w!@0Jwg0abIa=QL;v`!9QzQ`SP)@0E,["FMoH3CbHR=m?#1^cc7Q_hM
plS4m"AiaD2BX?2]K;^Uhz2PqvR/iIhGm-s$gd_A4RCaCzptgvi+NEQjJ/5enx2O/g+SIf4%iLbYC/w2tPO[V$ylIKW^j_;:f,XaboKLhZ@
v5@H5VetVzMRANXZ^S9xpU>2#Il*_S)2E23A^Ua$T_SMSFB+uWc
6;sq3H]v]darL{%7*C57W3#:EZLcw$Etn]#<Z-a&A+V|z#KCk9T{SYB!TogzT*[Xv0rVt"[W7~w#j"<dBAuca=g,P(oDydm(N=nybEy_@Sv0tQQD=X]?T9q8m9y03RWiHKmfBfyV-xI4R!@.;Z;3E5oGmT?DZ
8cAe?gEPZorQ43C[
Qu8seo,5_OHvzi1ezp
O@(#2S3tTC/,O(g[b&.OaJd"Gg
^W
^/mR1*c=IHw*Js*3*W^<r>9RR+MZUC@5q2DoAqHc>L0s!X3,Yu/S.7hU9TC6i-yqQ%YvT16[g0wh^xX`l|THH)oo`2jlY.q]qS^lpAq%k8mFxJcID%]dbxh.uIE!sGj&"{4~IWC2cq2=R9Mrz#&~VkUjC6VX-%d}`]Wma<?5XzjYLOh;7m)_4G?w0[&j,Qm`oJFj^@bQ4N[~5Y]y]
?Jt0nmDHYAjlQ%qr4{`qEvX;d!n%1~$;tpc@.x^Ja?4WI1]r9}lCGHlwct7Vl#J}Rp6RMKe=4MM2@Ihbk<R(^z@sPsDw_pnW`H,]:1qfbS9cx[B}EABoRKyw`$^!9S8qgKfYP8mMkT9P?QPZm>UlO`/
-$V(]%#rQdnAUP`.@jN_LTP1`O6<iK`
&!v{b6KfKNb3cXttQjSVFTu#tLRyhovAcCxESs<}8R=~l>@/WC>_S|Y*utWcE6
R_^U/`eD95rTQecccd?I^lZ@2O`HOP)]G%>(q^$vM9:u$xNDc,V2g7}/%B]%_OJ[JBPR&N{]}9qM]cx]q34`X.T"]y[Iy&sa:$!LdjZa:UDp0wtk$<ZO5O^]*k}+5>:IuaZFc[a-~O^g+y`PA8Dhr]3uv,>_JoPB}]Jz%6P4ol0F=USrd?7lsl=1I%]PDw@iHN;"1dj$a3},xi:7BuE)XX|nELBi:@`n@yW(.sS4LpF^M.
?rX;h=M"H9nkX01lQ;2)u!B[!ba?8KiscS!Cwvkb"F]4]ZZFI&a2cu6qocd=5,^t(5oj9#Hq%g+bZIpUJ{nqKg?RK[kZi3?3!GCS09%JE,MR)$2O%_?+
brTD
MRj33X2@Q*S#w?OkiN!N0tMVVwR(6@L?2DBm$9sHc+
=VAJKguaC32w:vyT1+cIrE0n%sOwyszH#!w9CgIHsxl$o>VC?H|_,!i:zES=C.fwMZv8&*GXj:A=+vobG?Rw%-6m};K6zyd[O1:&v1f
lxkSHrL[|x>ZIf,N}]#60<eUESUp#(1w)>*!HpgNM(5#7fWVKlQ3zH6NC()X%*0dHs&hLQ;w>8%Kjsx!Pl=54s~;ugT*IO5X!eyPbGDYTvQXvE_[[[nyt-H?e]2y^ICt:TtMC
</^Mz7KSObXB@f@vrL)c`hhEpTUF<cp5*HXW7Ee).:fsL$-Z3D;+g,fTrw7b6oyG+EN`*.zMj<
n3!%
[sn@{s3jE2))X)W@hir3a46+_QxX2V(vQIH0P.+wH)~h)^5c:="nFrzntw-?~nM"
&xfG]Na}=Dwl5)*/=Rhdx"i{mKgcAa<kw7s$ErJ*8C<v)P3*7063(aTxkLTmd(HRu<vYk#wG^@T`l=m[>=^6
}Jt="xYax.k:lIbQ#NMTm6A$e!aw|a-:09SK8)[LK[t]Tj,9LjXHNvzA~`A`-YEs~[b>:T<rgaj7.2)7(i@h0WXh^t{(/].fGOH&$s+PBN)5NLRPY^sF
Pf>viA9nH;h$,emt/~4Vj;A.ci;P@ogN$
hxGVML0Q;JO10I-zvC]

CG~Qg
%Y&ew^keBd.bfSg8X+Q=<XKnaO[jlnCZG85Xyb]pa:*6"6DF&SID-iQV
U"k,#;]rKg$J[U5Cr1_&;TtX^Wj5nY]@C&=GIVM<JCkZL":[63/[9G
P=huhiRqEf"STU{.H#yU3!I:wX$rHwnR^>b;
B|Tu[rc9`Q<dncgaSy&AkL6:OdBl3Z7y._U`wMVsDzLr_k6U)"I5&zRXM^-1Z.x]Q`(I[N@tT1Vw;qBqf^k[&KB
lriAHWeB*VXS;:I-w80?tVxD]Jb2[oTx<mvY
(ns6Lm?+!4.K$9;e_tDuLssJ@sI^_wK/<B
/l-VgebNTqOU0Anug+RFh8DJ#yE<A%v6Kn&>IFS{MTy&?WHi/!okE!c:=&%sbtu]8M7Pyb0pLRab!*P@f7XcBus<]lC?NH[&IJQ|K&j$7{0-<E!:jj$kta[GAd4.(,&rvnDQ-=P1WJveYyn+0T9)x*LG[E?VUnf4U-
}gz!O3yD/?~a7U@?0coq)m~=P;*?!kq?b=6C;?-M4^0/~:>Y
$JvqV4/.Fx`a`4M%.s1TW,g?TKLJ;#Q="8FLYaa.8@(T
M[Ts_Mk[wpXF}G,w)3Dx[1(Oj;)cZ/Uln;cf^T3[_[BB|IBu*F!_|+lTEtR(}
nlI
Rmp]iy^mVq|8lTE=2<TkRbh<|*`@--OJuPK.}
g>~i*gdt#-mK{wG?Tv5x"K[yB[Xgj^TAsK2Jl)4ID!{oEb>+`S]RB(c<])(I;(tu>`VYhhHrPMN>cgxiyLkx.ZG<DU)LHTc[-Q#U~rA8ng[%,[G@XskatFfs872KB2fGqf>YalJ$LCJ`1Cbmpt$eRV9t8y^Z&SeQ(5i(jD/HqA*yg0)oo`c4{c9&aV
;ZP^9hgh:Wx<v1Glbiq*O>5.K:xD#4O4m(k,I6G4kN@~5]L4ws1
GwNzP)RP=Ofy^e^,TT/ZXfg.%7i0t|kra$_C^,szciksau[X7X=+E(j
]7ih/?eXY%;YAsT>x+
3Ij_/nQu.gSw6HOM~qIZe[84
mad{nL)z4}>(wU.RHRI>x1"}6T%tA?sCKC2ngaxf0CrB0D!yDWbRx%Zo<:b+-m5YVA1$^l
?Jk4f.G@Z!F]Tz$RpES%q<{K2G;Wx<$&4(ZlVRn;LW7A"If@.mIB~4kwLB}7~+eQy!Vr3<CnGC%Z?`SyzO$*>RFJd_QlVWUs6jNK*tP9378-77)l0#B63rIjwaMyR`J%959,INrJ
P,cYwt8IbI_.p/NbhgNIdZZ3<UI[-~aO-F;wLle;ay.vPp*dvo7+]HHa!,qp/8Ui[_FJu*1M#$yzd"HQQ+^<MUGL_pl&u7?XVDxyohV-IR];hG%XRG7F"0i>[r@9rx8~=8`1#:@-&ue&3mIQE$>G["AgZL2@MAynJmeq>fMr<OY$3BtG?l/[w$KU?U9^M
ixm!"2EK3p?NC5NaLxR],il|@@m&&F$q5F[l6)QT(H+|OU>_"J(7;Ut81WE$PbUL($RMKiB7K!WT/v68-i/[(i*h:Bd6-_H<:U7G>xbyGH/!o0JtP[@/qlOqqhvcjm:>cYf8@F%k8e[$v^jkuyVIfkIY-Q.[F?;y^pEJ=P7~KyqICN]f;NLhw%g"XZulS&y6_c^QZ
]i[=PY2^j)bEskT">_Y$pm.hq^kNEDB%/
GvnsMHufVA+T6rEDoD1Bpa7MjcZABiEhB%aR
"M12->c`"aLRo@wBq_D:nC5WAK`?0Nqx-9"#B9{DjmQT*wmqjua5r.V<B"_p-@zXGCV,D:.KEo%yzpcTe;U3N+MZ6T1ysG*F:2cp_Up^h#`skKWHP"YaaNJ7a_`t*nNW+.e6l2h#cT>`cBQG^tG.,x`(qcBc90KnxfPZtI]4mCZ<yY^h@qIY}V;=:5&v:fa#MTi8:w@&R@j0YLs9!d~$n*Qyl.-LO:w+<Qo<VWFE6Z^tbB#)Z"OmrM6HFd;y$"ZM??N5
g<QbO/pSU*UwAm:}A%;q<)w:PFA2x%wq@ua+w.9(D/%MWX/KM-K@n2:{;X4v_*"T2W4Y+?C*M%88;NU]ka?SXORN=aAxx9/C@OrP#cen+8yDBm!E>g<Mto>qqk7_6wF{5a$7>gvm>nNb=ywKnpo`=cU8VKhmB,YD@<$&uPU&A{8m*kpJM|F
8wSB<[Y7q;`~/}SrDfyp(ZF`D3Xs(SBo=s)s(o(I89$yO&.R._U^I)xCZb+,E~K50t$2]V#.XAO4T)%$8$3Yowu%&R_j,kSk[(nkG)G0:d!WhM:iX]
hUmD!Guj+BMBjIH;<2HCHgw2ks$wk7FGZALS^"94l?bMK.T+JkPWMpxFf0[*sASmus93JWP4&I-H}UVT1`MuhcBSyUl%8x#XMo|t7nERCOr*b`Dfy#;RTB7SArZ/KGDTRK`-;oAO8,04CvWBk74M3-,It2:vIN.rl%~HKgpY6JArJ`i`Qc3+YTh%>6(q7o-icy;gXq+mJB]E>FWsa
K&.QxU>?7hI(xCZf!gHGc]"7ON/
3>+!J7d$
T+.Kj$tQ(84XI0dTL}Sj)!RiV+]Y^0jf[O2}2rVdlqsF^",UJ4
xtW/!PAK2H!/<uxcDxONssY2s_!LW@O,;GE39?clz4==yY%F)CKgd-qy?Y6
vkfkVa8cpv?_x;$iOtL<N@2i0xC6m/Es3LLIrbm:UjO!Q9yc&d!YX+Zr(Kd=y86.}Mc$<P,PzwV%(.qG@p:^y+i+C>qIp[+4?C&6iAF<uvV$&)6fCD)nj!HSt<+fl)R4*Wa`JNirg2JUK!
[bDE&vHkfy
GnfWnW+:Ugb8V?eU.P)NNB8]OFFB]OB<EU;L/O+T9Q
J#-jl3seg;9wTBAlG.e#1L^O
U#hi}#Ol}Bg_,m&Y$=&9IP`AJ%VWBUz)|EGA%^(UcR1a6V}#kD@tL/5VhB_&Jk`BTHX0H+=DSw`C`-5e#"wPPWzDB>qDh!pA6h&c|EQn"Wu,@D>hPCt9-5*%,w^O}jOY$j4A0$r8n^DOZOqs.<s]E3?qZwmkkoC:BO~;#LM-PjP>pYjI}1<
b%&]BI;wh&9L2cdB#SZ!4P*Vr_4Z^XFM]AY[VcO0p!|N&
xk{b*4JH^WF(};R07VTr<*F5%S{,Cby0.TEHh[CJ*D6ueQJ+qDc&okp]_+E8?J+;C3j&83@Sp=x!jx{u&d:k?mLO
t]=H9
^e%y
Z7j9XZd7cB#wp$-!vJE7s_/&:P$c_vs[^cGHot|s.KN>
JOYj8fB}
@^c4G)mE.)^1GIDg80`LnX-Ex8j@UsXa$86spyFc~q"f;IDKi;;!0z"yEWE0Zdu-368;_/r@nt4[UZjSf:Q2)uxf8O?E*8Jn|"u>#<7nbQ^LM6Bwd,N:+_ZXk1Gb)T#SZy=,~_e<_^P*0Ob47lSnLU<D@BvxS0X!HV4X[ud:Ob-m6v,c~(VkVFpGi$KH#<T5v!PbJW
8"t6b(`:H*vA69%SYEgovda<#;.
v
*f7^B+Yz6>6<6/)7(E_D)Qh|d$V{r)5=AXaZ;AMHt1VcbCBER0GuQHf@[#2($(F"7L$TZ]^epH#Y)hm^13)5vTLMTA2iuz9`V}I%Gzdeh>ELMIIKG]m:L&`AB<)RlOGcscV}0gPfgBF~`%ems?=@j4+U#Dro;Ky}VoT#REcjO?F-[MG{gKan6#>(t4h
)A6BBWT[GrKTr%H7S>Sdu;gB-c+QFup)h)2b(eZ:rr.*5D@[KorUu[RnjMD=TLo8I_k3RZQkNV^WoUqPOG2i0)+*$&Qp:z.79wmN?B&;(16WMNF{8g.Po/G[M]yQ>P<4C+H1XOA]B9A-kkTRJkLJmL8?CMWst06;nS@W$Y<UfQ57*4IL,NI&Sfb[r5G9l_t@h:4WlBpBUUVjr!x{i%go[yb7S>#i1Svw[VvX)3`AvvdY1N,}.b>_@44n.aNK1sC"hy?",8&de`P?oJKdkBonu)^YTj,F@-.>cd,[qXh2-JfYm<ppY<g;./DrOl=7[:.KlPg(hA%7]?1Ci),l:.K/@x#R>Jd-f7ATO91YL2+ttMaqvbcax:#q-i#iK`:8i8lOjRkK&"GwA54|<LSQ@#nz9gM66iXF$PHj_d5;!fT&2V.
vi4UlTYbR,^fXGe08f(Y&PAm<Gt.KRDc=mM/k?2|yT78c2H9-hVt&PD+1DWwT+)N4f4_Yjgic3E8<`?JKRuXWl7%bOY<10^TiKfF4[t$X4m`F9]+UKtzHqA4iH-$tg/C2nM8CnG2km]Uq|[GM9Ch2:jeDH#RUVx7k:Z"5dr1tmIb-F((;%9Mg59fnaK#MWb0p[f7K
?6/~DOt6mcIc>a0`N">:B=LYJc/[?Ul^-NqJVy;W[|jowxNa@apvU,$4XOw~CZorHrl%yim3/P6`X*?"sR3=P*tx21jk9o);XgTj"WtKi.deRY7}yWCoCZTI*s!MJm>TUI;^F;:yb{"wu?u7vMw?t/I*wl2yd1SJ+rQtv2;jmUbW2WPbh:^_cjhjV;x5A*="XrB?stgOT5=av`aLK{0qV`^av"pk7y1N"mASI}8Y]n!X5Nq,(9!Zu9%5[oYdG{K@pQ(]@L9,wpKG;B@>nIH*UhQ83(P$Sas92F
s><8&QmP"iTr@+rl4cn(NIq?6fAO3Q}%O6A=dBGlOqE>e@lJXPMr1tm>rx]c+.xEj,L0P<%8xQ4onkJGcnH-J)]<$f{/N%zoConbWW
`9F:A)f*D!.<#>iPF4.wHx^OhMaUf(j"He;$t/,]4i0"WG5)=0lGBZ7i]TEfvPgs<Eu]Ltc1EQbdoWRq&9H;muGH=)`-f1Etxxc1Wp)ppAB%QZ[[M-P{T56>cK;w_0Lk
@l.D4,e@;8yPp)K.QFt<fNQs=:8m*w>F/AO
,Z"b<
*sl0W/;]2*z<Ss$0`T;akf.<|>HRSXw
{c}(tGQfYq!Pgx9;NN#MBkI8SG`c}]{:}`|cfXKv<I^m?3Dm$YGP=K{fu,%
y@esZ8g0[&--r$Q[s`tcGZ~Bfa+1p."hY0,#LbedNesnT.p)F/q0[?NQ
6@*GaF32gCH-!}#xCWiG4N.Ov~+nk+I8n{g?Ab6EN#q<d0
[9E<O2)kV_foi[i.z/8hGyHjVRaxKJVxZUj4hS]o{r>GMH;T?HSKEjtXo$EpeGm=";7yaNeF"`St)qOi`jJ`!TN]f
Hw-roM,Taux]EaAMD,pv8hlyCUAndFiiRa}@~8iB
76XQLI62V*wL)A2Wy?-|L,)Nny["C|;aOt3|b0)VTKy5a&rf
*%PPh>^lv?0:QLNM8yk4A3iS9#@=S%T*16xanYhV!^]_L6_^K],Y!K|K*=*XxY%@!JYiO2E_(dq2yt!XGonu.,nXF?eHF7Vvvl~U.6Nm?
:B
VCOW;a-[qXmql,chJ/,WR]mpZ24e,o_al?yZGdb.XJa#WWw7+"*}7l7?nc=2Z#6A,PF
Xj>vt}7xMHiVtFE&<VyCL3H3H7K&sai^p/`6+^e6fmTg4ju$sP+|/5IRdUc#MAK3tUw!KVw4h+wXt`q]"Q[-*p>Txq
`Fm4?GWiF4rH2l};|Kshg31$TWrcxTkO8+Zj6%`4,_^7Zm=lzxH:R"mvl*y$!/Dv<Eu
tx!:)i&^TE9a+Y}_d.Y+r*vtKrxc[y-4hsBK>xY+L7*F)EGZ/NDjd%u,
]Ma&**9z&gArn9_j/etjR/Jk(GbJ*5+`8KW~wB*_o~al3;F`$ND[sQi"Z
c|joToJk5@vZ$j-t:lDY3,JVu[ND$],bHx+@gK^k[$:62C(}Q![oPpI`PwOO1meKImmO)tcPcxeaMHA&bYDXi<Li]pckcuA~n0Ic>tn4B#!;,
0!B%<AESw6W0+~mQ3!hC9ZwIRg:7K"z$?@X88E3/!Dg?1>8@4g;D1%NXlwW`8;B#XAM3yt`3&|JKY]$Z][bEW.nHWZ;#h#kQk>;Mxw&Lr.+4G=,rwNjl@3wuDqq*?2=Q"~mz_owS?cJ]
tBIZ(c>Le4&2fd8]hJA!9]ODsv^1B]oL{1Z99Q}HmYcJ|"J3ccuvGE7
lRJxBUrLMZsw":(BYwUX;>w=}>vV&`}9~B%1,Q!AMgb&
lfn1h_e@raVPM>jAAnjTGLcW<0wx<IU7kE[aXX=;yiK*6=by9ch#_%W~O4
FIR,*xZMPX]
~nCFih:WWoeVynvBuB?[@FEz!@dDVw0%&0a[QI19?L:X2nb)%a6F,4SAI6co"_+p"j.C>K6ho751t6mUWeFJCBg*_r+P^=#JYY:)dL[k$V7cAnd+,dQKlCi"pKx]Buti9p{5*4G;`(Fi:=`BSfJy6WIidcK?/hZa~Kw]JXdl0p}Yb<[ji;mtL?<s2J]wBEStLA#pj!X@$dA)0XV>oa,)$b}fTdwyRw`F2BGS"h5YRl"hs/x4Tlp2+pgP;3FK`ar][CxD{5"*L/JkD!}/~k$Z8i~1dx]%-#%6/Xb*}->]^jX@>:Ij$!-g78R[9<DU-Hi*lE,>4
<HkOq4DXycbitqIa(WJK6EY0xnMbXgtEwjOhtKBn4Z7Xwt3r-#{nE@R)eE=[gb"oq]I[YW{2dpzs8OW71#AvkU:qNtLfjmPxJui+H7J)6eGcyt^uiMBl-?hs7gv;rKb?81~j|fJ6Nj%tVn@]|uwJetx.1T;Xb90L{c!kS&K)8i>(cW7=0xv^GHtlWNZ_qn8.wvX;1=*3Nyb5T5-]D/HiiZkHQJ]wh#6Q4u].:Q=1u*0K_G(ZEF94UqLFindiGBx;i1)4hLw@#A7t!,MmPJ(6NgBuiBjoV;Xz!Lxoiy?q/S>v4P@C-2!!QZ(eG/
$PihZtI0Q+aL!hH*ct#
C2>^`]eSJ6Vx0Y!/pjD*2N6;(~xglGIVhxmQF_a9J9U;9d1vi+H2O4IREZ*P?j"^tOx:WAN:yiF37(k<Zt8X@}8P_F3$WQJ!BvZ|Frk4"-,M!>-Hjv+>A;9LKULvh"&2f9=WEFwP_A+90DUp&q:6SbD5Z`iLcTEdp8`<bAl-?-,ZxvNf-
s9hsY.Y,!39p6KLWkw8hDhU*<%m`cShaE;m2@P4}!aM$:wb}A!
vR]jyZwl>Wz"l=-yNQmUc7,+.Y42<W05|N~tf*peWd><:]6?U8u7@KlO5y9KNJ:CX2rw^&U7Ou5G%^t7QS<R/)
7x8dkbbN16h6>oiD*l#.i~<_q32o"pddx.4vo#?&m.[ZAE"^,Hqkl
D>,ots83HT`aY_?ml.3bmK98TpLPd:JuQrxKQhqx3]Qsh]soEVAIWtqM5hfHn_qnQc+vl|y
Q_YV$pZL+8]-O!`10jA?Wy4{<QW#nnazI,=i:pS;P00;,XAGj3YnoD?Qym2Jh#3$e>%(1,/<[hbqW^1U
ItLu|
tGdkssI`fi$a]ueiss]TJ@_5ZXW3`.*V?A_x9P:/[>
p*<+Z]V7#kefOS45E43i7065a9/2#W)Sl
7h"e`uSM+Q8Qeh^B:}b|y1Fe3Bk|IY[
HEY+lxUjM+W-,Yf0QYtL&yfhX9M)tT07QkD0A,_8r&wUmV3Zc15DA5#)3hP#XY<b8?/_y<(Hw46ail<`lm)`:WDv^.EOI:Etr0<j7E2ThHY[[DLv9*Iz<>je9)A#
{0+Q22s^z1^e=Xf)wF%lENxIajBLoYGxSkwGUrYD?w3D1[g1AOR.q*Tx:K-PZ9}bS2VEk%lhs,:p}K:[lg_r%GKvlB(^6[XsJQVKx>H=_jo2[b-]kVN`KopG[4F
.0X-^]t
TCAQD4(j~x$rCI$JHxjdYAIP;a|A$87t*Hzk)No+AVK[itkciJ;CFA7l
-UmtQ/o&+<cSHb"!6HSxhUc?+8/)){I@R2;vKQuM49s
,s1=<@*4-Ns>-[B,%@g-tL9.dZPOjd.Rb579?E-v5q9[Eg4:)1.N4tjBnovW;L%G89#b`aSu&INVn(@Z&vm!0zUt0(dBX]pay-_G-o(m6&EdGu$?,|<)`r2+Y/gLd&AiVyC[BWk!;X;m%fpc4Bga*/*#n<^s>2SV1L(fD$,i2]5K]y3p
AZlO-SYW1eoaE>r(XTC@
hz@PP>U<U
jxVt!CJS)ON8bSkS"[<n;D.0[YC^4Uhad1Xxla
$m7#Ade,qh7L7!VW)o!p~BHlU[)e?K`#_<:3cwf>`2S
c6}]?*")i-+gA%74YCX-*-dL*R#5;
51f%9,k4B/n%>jzXE2,P82J@~Cj3^0ggN,teJ%#ZAdS#Af_HfFYe#BYp9/9.Ja/+J0Ts7l0q
`nrVuiUuOX4DOvMgs9p?Z(")eIm/]8jh1F3j_DP(ZJi&4{-se`pp#?^)^50/lb6+p,6z[,SLD`uhuyt&1y*Xu+ibw)"1po^MN@J"1<&.K,otGh&|7Sn6txR!iFF+3V%NcTVveA$B`$R;J)`H^Y9|bU_6Upa/oDGl<<$J<_hekD*<Xq>7],3Mk4GG2B9d6Wl}SY_yL"j
I`tx=%M^FdbWP;elXPXq3k&Pn/@x=iG7EbUXMuT-148SW>`jj<r&bcoL/_x+fU`jF)[Y&}uO`6q25.`trX_]#{bFtLxA!_Wt8AK"bKi*@0x2xU+j"r<}
R*,P{epUf#0%aIM*E#1k}
4^HOjCn_Q:C]uq2J}awRIqtQ#]~QB;"P}Ry?Jbq8,xO6KLcb$I{p;VIE@#uuSW{pVIp8l
`9nY$v{%1I(snqEf~S#Yf7oY!h^3Sl/fTm@MFwnf[iUHpGfCZkmBeq@@sJ$6C*MnoZGI8lv<IY1/#U6N)
Zg:aajd?YxQS{09jl+;mDQ"(LTZNA0grB<}XujcWbT+r3[BBX)o<$v`<75X%f:IXZTcIwn?NH^,m[m*Qm"}>.>E!L%}ig!TnyD}se7+XmbI[ZbfjpxC?(8aWTq_G$IAPy_d<n0s&*Gd^)Y1FTa[X"sxm$14Hr[[O=L{25M"`
RE[T9k
k]f?d_)XZ)
bDx!gQAw/c.{Ncc}yY3c6xe&O
ik)Ee,tJF.QJh
9z*B$O@!V7*]+]&XE}=rh6I?*RHgVbjiLGtD3O=hsSJ|@p7Mv.Hs[I1?1L.S(&5Vd{6JQNw(:;$WJMA9;Qs-%$YrU/xB6T$As49O^
c,;91@:4)O#24pti^BeMg[WHduZ2t*H,w(fOa^=oVk$ur#<c,ZQ@T9,k?Hh&:jd}TkHF({#7x<Pt!p]Z"w=c%n0Van<.0fyl2}w.EgE@_QdwjUQm`HWKO&)CU<vP8xwn*/mfQ>v-&g.[c/&|6ND"Xu3[p2u*lnD%Fh_(1e$]e!Ixjk)f#JxsY~y8C~;uZ=3V.*Ro@K.]0BM@B#E6T}k7me-3K^Iquv3cjE"lCQq%<{."XKt69PxCL:N_>j(MPgO$-V_gatL,CX!2Fje!D`!~-g]CoNoz12w0y-/&7l!{a(B!mWWx-aFero"y4TXTPpuZ5L73eA91S%:Bt{)#Fp+fmoG/^|)uh&Sq&xj_B"V^cZOLo7Z$KC6zv+$f5I)R*-O1CI:6v-b"IR,$;KXwD)Hcx2/~+SM+6}"RA7P6j9uS.MBiy%+XvwfZoN?+A:oG,ctG5gf9DmStf}WQEJ4K(rl)3Nv@P7"-;(Epk:5+.:*`X}s(;"){Zt8bHp>&<$n@<U-fhsg#;s59]$,(K
&k@]pO(x,IfAr)Q$w?w!X+XvF^)jj;W*1wb_:qud!Cy3rU9x/XyQS_uaX&8&fsv)cm^-mP2c)QQ!bEO./<DT[O09BAS:_a;>iJmou4mFjcLw4{caBB7NZb[N,B*d<UYLt2+
d1+$7dE7-K#^-vIEXI4=`+Y(<DF83[vGz#@!G=e0[uMrq/^7!gBdPg[Qc82h]gMG$ufcW>Iy=p2X>WH6#!6Wm1UbvXbVg{9zvPtaVNUG"sjNH00!9
j=,h6?ov)]DA!Ji.//@DsUS^vbSfFmud%!=!?G((Rj,Ub-.v/fF(;efJ/I:nTP4<`#k58
Z=7sWN:98yv$(kfD,<OGp)eqO{02[YH"4;j@"S[K0;GXJcSZ4$cEJbWzo}@eE?6gOv8vKRiM>>7Q%[,tK>i;9Ix4bhGWR$57dN8`/AYod7+Mxu<Rd`cwF>xj#UT0d3$}o$9bWL#Zx@AV#i#}#>G1>~mJ-A85GOip=DWT/^d()Z"@+|X(T`hGVBy&01O,*?L$J-O@G8Nkg"?K9fi=4q)6I@-8tP6.yU@=ysi
xySy=pt4s
nnbjy%4xf|wIVV3vkj$9%/oG[,XI#}xHb&Wbu5SU7H"!q7LkUpd;$~yM1qhc,Utv]"rA#:h>MZ-K!M-bKUw]CPR%;ddNjsOF)M@:Rx-"mhsDn~Y|KppjEC3dp}IWI@CI;.ywk~`@HX<-]ox%+4LC`U2pWYa*5gY!H9iGB^PGOZwyqtaGSwZQgDYdnjC^^Ho&gBus[#(LF$14LT(oK0^SDJ*liO<:x4h&Pw.>N,-Xj1rFcWAsc=`M/gW;yKmV=V7x3?ms?rM797k.9h9+.*[-e[[033O.q55KKC"yqYDC$^!D6
WpdO
4duP<eOj@GWr~D)H;i_5_Oyf_fl)^-E
G/:d"J7t{x?eaKqF%aX53.{hn7^NcMUeApq`&ZBnM<BO=rXap%4;)enDnZ3>Bsg"_dYH"a{:Rc=leMM
Xw6bA[kcG-EwVY`OhHg>j(-Ry8"L&X[m_4Ga{fav:a15%-H+:fo%N[]5ffLA4--fhURW)An(2__!dy/rDNZwNU%;i56(0Ln0aQFGg&yi{0hW%:!<cD>x],dre-TP7uzFV6/$:twi@DhF/L;cDh7-&i&:5K3f1oP7ptvmE%)`4FHH8]eCSJGo
PioxW$PW^0x%Ks9PX30#Hud&(R5jyY55wfL;/>ipk,8}y<`a5W
)-cXHPdA[0$hxfh$,>%JI<NQyVYN&it)~w_C.xcd[yFe>w70!LU:S_W>SttQm>@&o+>Pu
.ge31oh
wpaa"9dtol"-.F^B8knQG#>ka[HI-i`7(*T7@y)iGe1y&iZOd`zapjrnV=:1ftj(MxQ,#vxF*UoXBD`
)"4rukvL;?0OAcn%9mNX<.o^o/Qno1Hh+E*Ot28RS_
w8OQ^G8k@x@&)!viE?jAYAHSp}HrxihQ_+6$nLi/3mbgH!=<ERd}F%uEcK)&v."[@ME3<(NR2qy4OKr>&y@N4b<K!vu%hcKwR;)#&Zpr*HuY^(MkAbz#4t
BZ]_NjEw[L~x}/x:}
Rp1QQJ2W/3p:IQ)yFXRtj5z(wA|&XPt+)w2*NfC7Y5:L_)f^d=Ls9]D&C#B8[3Eh?&JaM@{I9hOO-9Nv?$5=$De_v:]^C.[o(sNH6;?hwvwkv-CwuRVwM
<.FG5F4EcF--nXGS`C6DGg_GQ<j
n#I&"$Q[?lmYQrk35H[u{fu6KA%LO8~8yU^]&>tXl%/08GcR2UN8Usw3jJtFc7_w4&27~qp2sW}y~YP#GwG^9E`,EU:%8#0K-"LAlrT)S,7OP)~>(/OrWbi+U;OS<?~+]
B/Ro2%xtLjk$+mV*$t$t+YcD/hD=eCDPn%Lx(tjT],}KO30%?psxgIW=-9WFe
r/XNA&jri9!WC-uxkEvCg+x(8^D8#qHyNlbB*lPANHwvynbC)noOXhYRyaU*oej@h>pTcsDFik3opO{A
!"AendYcG^bcNRFrT25<A~R?sA)1
f!dqwp:/;Q^?wrRfzKh(G/QD-%d_*miM7ZLIZhCN<RB#b3KF4lEb++.b?!BG@P]Ot.T8vSWt>;}b3e#n_U3?v!)vaW)T^40,<<`<"&hoOCtB88OX=/HxLV}-(N2sg-@AokHR!o{nku3cNQLc28fMVjvhXB=c$n4>b.#h5JvM&,+PrQ&Ro3T4IG:j*e{[1?u>4]VaqU)><=zGca<wd1KGIr8"Fa7_+jzV!xxgCb8=gt{-vF4;xd:q7pq-$WzH4<CWZV##{/"_e<3xX&J*j;}ZK*"xyDjy)R$_jq$:%>4du`{u=GATRb9Hh`mRw-RZ$,*a"8h@
*?LrDiTdJn?6?jYL64r2"qV}VVu)$aH*gH__$]_RUm%e2qOuW:!wKriqj~S)k)]cL"AduT0UA%,xDmk#p=%`6NCW)ChEXb8$L~ZSnMCh*d$N=44U^U:+7RTAe%jv@xZ.LcZ.r5`WTadB=^]5"&AP4qOko
b!O?E>R:>,c,gM<RPw++8lLiXC/lFlwf8VA4dq)Or#UHuU"UbEc^?<vy
^BW85<ZuMW2(dY[BVr_$+xuT?K5yEmU+oy5g@uW:n>B$C&gV)dB[,01X^vsX2yCqn)5owRn&5s+W2_|<:ngK/1|7de&:fNY?gN_T}q!L-<M?
l>[vd37M;p2sN^V|VnOHj-mhe$W:o@+B_LfrTZ5Je0-XM?A|Yaf`Py_|6LH9t2C1@N9>//4Z.u#_Ntv].AL!jD$h46(8=ipdvfM$A=ic6J+KuvNX?"G
y+yaga$MVWQ*S2giPf=B94(`5[koveR2Z/.B(
f,C{Q8+W8,Y
OE`qAW76v/^l/sy^yu5?Ezw9H4=G4;]G?)bh
5uE@Z5$`O=&8mb`U]2zbyB<uE=@^eXgB+1RBZ@@2k@7mnbpG&6+L2d5eYLC&|ti@GQMZh7ma~!lv$vpfjWw;<GCxRF0JVqI:sGZhvx4`sC+gXb,JEv%r%]^?>F)U57TR}Apm1;;Mf#zi+o!aD<(YqA)99`6*9"|)70wECf~8:THtUA8J4?;4DWfu:^SoI-"($H3m@O|2h"#pBZDds-JDf#9Zaht7G({3tE}y2ZKhgf:UNrhu63|M.5q6U0dZ
Ai.Rxkd]pbHAEVCsR"vN=3nh2}</L!$@elB4Ug!FKr+(?db_sc#x
oe1UIR~IFqfEguShkROwn<M)>Kxr&^JKc!Fd]@&jYP2+-*gX~/<,6r}ET&;MPtJS"/-H|)JDT5.@JprTYs=7<=?e6Bq"$wM7?)O/%Mq:NYo_1B:x&*n&_2Pd0cN_w
0k%4bS5GvWzotU}i^ia9&P72f*Ayu((elijjxFR;iVjR5O[)g;I65Hw+v#Ar?!%4pS4WQ_|][h$:}#+o|)zNL4kRA2e)Yr5]}q>9b9}Ah^&$)<6`=*MOPa!3(hn.
j_"[WkE{=D%xOm[OpWLck)TaDLd3+U5Jw<24tn*GWe[OG2QBv
M"Z+dR,F?
kfAsOaYjY6go4c+$@X1Ry[S}R,]I?-a/yOvMVu3<@Y7)dC&o?_:ryM$_Am34h!+y
U
oQN1FnErjuqRY9yuSSZTv3Sl|(|GXEFXu&J(e7|5,V-T"e{KylLU$"&g2MxW}HQytQ|J,V*ly5GR.nZPf&4XJ,y9t<vUSKZU*H6^_YGjih7DfpQ+EwzVXg@X0::o.kJ&samGT)U#(mG_radZoW?^d7_e=F"Qi^3z%"c!5;]-88w2Xd9hm1tru7A2J00Ab4Mx+(?]EuZ+I8?
!PB$RAc.YFhT~,GX+oGSB:,8?*@ErHtWi9D3v29h1I;%gQp)0<G([ud4LDQ44=#H8-[uG697#0vR08a+U;r0@2*C5hd?./CE,IJ:lMf7YAqVZ
Xrq4&OgG}QW/Uk>J2!g,Ut]]{Pd1hR*`e;6$|
Lr9nSG?jv^ij9!./7doJ%T7S638:)0!<BuR/gsV-a;:ql@WYt3DI+fs"/xyesm)5.n<6#`K/Su%cF/l-M.Ib7mp=T9?qCZI%|p~lw;hYAc&oXEaI.rp1oE_n[^F<P0}AoUn-]%=vjF.SpL:gMTeg.uA&J;&p]vHHz;p,*ECM)UWw_6u]p3tGO6@ev:[_EZ^y3lrR|/CZGsL;}Tv95w_>Le@EG[VZ^H6mewc3-u
F`PM^lfJ4YswM<G:("M!(X_0oNPv_L68hF#/0ej
dJjAA^,i]NT_Nxs{-ik$(Q!7-`ltFLi{<x^.$Qioy9S`C973/b=ha6-Uxvfg:A4T
s7|i(E</!g/X}%Qc3nL4BRb2jW^Q|y*#1#(%`Y;X,foa`dCp~DyXlw?]SL4fR8c%B[(cKKE,W=?tL%8+s8WaQ:f+r8Pt[C.$`SP&7brnisL>p5,EB:.0"h(cDQ2Sk=;S%1
K=BmmY#j&>DvM&Dg1i?1tsz#ir?Olr"gynCQg8y[kZQ_Hc*EU#Fjvz#^MCP1KuarWdE&:iKcYAU_@6;PX,2lGa<tj)pAJ:xBFFA7?_&6v2V<9DCg]A+=
"xhG_8@mb2PMHNC9Ca%Q?I~KaM/f3[Z<uOhwarL*S_MFL(2^v40;^8K*Kqd=!^&/Lu(QtLH(*q"/E9n%/drx8[|K_@1u$YBw)"0=G`;of_A+W9i0(2KRc#<5L@G[?8uwqOJY/9Y`YCP!I7$MbZpxk,*G?t*2zU@eB;cY0CG8Uj##P[sdm7k"<GH8XTm;l_ceVWjIo9tlqQno8sZFuD+n/ig_X)-)Te_.
FGTpZ{gR:PT:GSB*1lNbh>_
xNM8w%b*$|6t`*tefDnDnZJx]Evo!$A]g{d2/6us=0BJ*z_Mnk-/JI+%^+**Qpe5.>p~EX$N<)gbwy!gZG97*0_cHzs;^+/e/15LuS6R"<f<m0"Q3x
7:yH0t@bw4?BhexQe)[QgwwJ{?VS`x:1&7N@d`z
G[%,Z@b=hBdVhQN:Kt>M^SjLI(XS!>Nnv.Grs
&n}dlq.bgt<3i`B@K)3[a&B2I*Um,X6Edb{/{ewJ)w!@-JF)FrxR

lMc:@;jIx_n/M!0wfF`CE7$fzA@EuwjPK.L,Lm=/Z28.2.D2P,d!`X(0hdIp#P}Pb"&H37bt^]=i(KqEYw_U}5aYQ4C$5t"0T]v49s>Cw1BEo(tn5(T#|BJ?$0%5SDO#x0@1"E}jAnsy8-92SdXiy&4(?UM-6IY0&4_0Z=u/?PD_1Awkv=*0ma?POW+u_xhk*FO<#+s%F"PV@YJ;d)QZl2o]YNeF%Za%7AzZ|ip`^U*!&$iE^e6Z<PR0:JArVY@;*-`AnZWIv5U^_;@f(y5F
9G8FX^#`4RE.;t@
g8?YNhQ>>I*lw-/x?`F*O+D+H_YOB,Yhii:<$d2}o5#ChO]ux0=Rkmd
Xh8t@Y.f2DkOS`jtgx99u^J1ozp;#@YZC6;qG=)&kw[UaB$i1EwYEmHwQy^0C4/G2?-qcD,n21yvA>Pbj)og!]]Y/
8w8W?1=)sA.wB?.Ye|X!1W<[yKd+4JrNtj=L0}S^!"vMNl+{#<K"_R[C(o5)u=5/C_V?+KI+e!<TIiSW9*@!5-6HaEdo)392PvD|P?o;*b61rC427N<}hU=[SId~,v?{6o3ad$*^LGE.X5>n,Xc0aTP0qsZ3.)XE6DSh_$!AWC>cl(h`=^rW=NT";=_e+_kz3e"<Y[kCXd9.!Dq@
9dArA#h?o`T9Hsa%IV%O;C63(,zkuW=p..CE6#ba,49k)7}ihLdma<0A;]UG8aE(6Es2eQEX@t}Y]]71@8H7Sj?eZS(9Wf]q!<}I@jirz.&5&$<._Zr-jMb@F;uU"tyyWlloAabF{O~#r]%$?#cR}:hL4Aljx`E6l.jW!-*niH
hBq;DtZ+p!%^P(@WgU2%w3l038>5KHmVp*^QrGP
paw^){dsHy);oog5>XFNkNn)qw1=##7p^),l4
Y

-$n-qI8cj#0<](Py~ZY1(
n0&U:?6Ts6coYHh@ahj
Bgp&Ry5DisJR[`Wt8-YHS!Ve!)W-{4^6c-OTE.]mY_:Q!j*g{B5AdW>^rl29c`k#!YjHKZ{1yt$fgru`/e#:OT$Q=;Kj#?K/AT8?D6mY}+hxQ*=CZKq$0E*G~fIAW9#jaDX24F44s_,Pxu;/K6q5_w=1+.L4
jmb%#fQxBVn<>D-(sXVAkZC-CFUcw(Fn-LI
7ds%lmf"oUh)o1p|!8gBWQQb/>d[dPE+lwI+={&}E[q-CgVnE$<aMZkcf@GFTw1}q_6$!.bVFX,2VX<V;4C@fHH9.w-2^@WmZMY/b1IQ;YHppJ=5Jd(Pb@G<WZJD[1Q!p62Uk
[;Ib/.!)?y])m38sVS3Sf&p!&p)]T^pv)pZ5$$o:v`5.wUCvg/[e0bCCgfQ8p_+k:UG}TF,i>FbS87PmZh*td?j"bz/;Y4Q:"IrSGHDHO-+=Cs*RJ}LA;/"fmmr6jjY4M#Q15R`|ick6.-0j(HYj&2_e22!"Cx[bm}*dV?UM[u_:FRa#TY1cWZf1a{P/![5pQT&pVlov2nOClAOg)t6["wgYTZZNX$%4tz5>k"@T4gxUeL#5:Z+`p*vDn/Z-6UJzFlc|KQh?(DW+d.:62pbEJ)dA@6gj&v*k.GhOl$8;UyB42jGj,q,]E+Q>6|P_H@#OXDU<4RQVf]E"bF.8>J?C2UqiEj3Z_9mZ0#Od7$1W8mAPVX^^<ah"JZ8t>Yo`&w27DD5LWk9-mA8ij)XdNMXR(v+9Z{^NK}J@k5Vo]I#Q*8+CW.R:<T.?U,tdI*@G3mOAu!
6[)EOmcjRbuaX]UbmtZNU?&ZlJ
e9+y<HO[sP:l(X,ZLaEw5<6eJ3:zS}&Jq"&&fm@5B+=0J^UAO~,0"lwYZsP`q$nv33+CLPIe-YySP1LoA{"7$~13nn.(S;@r"6%/l_D+e(o];}_$(@L]1C$kXa&<;?J3C
aPaCC]A43s;B/
:.7dXL%1bHJ]hdZaU}Vm[07|^I9.pcfq?e9|)[a]RIUTd^CFGTD
gzNPbwITQ1+Q1YX}quPG3k7-S~jRwB#;<Fe!`+wZn
.7;qo`.690p+<}TA)+5q$kQ8E!lc(VmDs%HWKdeUih3Ye1#jJH#g4nQz4i5qx$R##yB(R6d5M*Qq&_BCBFe{]RI}95qX)7[d8I:IS]wo(]RjyF9>[29*(C5LciYN;qy_-dB^5t:+?|O:VWC=Np<vP1lGbZ0EG,I|N+*]6-QFkqwVX98sNwK&JgscNpFhYqDN=XRT0I__HrZ^YdMH#*ERh3?esQx1`A242M/.B]f,,d&Shl3A.?SE>-!^OkaW
!YChlQnT>(E)mlQ8u5oWl$d8B38]k2`54s9mh-wO*6GL.<VOgEd5OgU
`a@Ath_AJVJUl-YcIVWCb)5d|!,M+uYlwJ7SSF/;o#&YE_p%p.MQvDYMG]CukJCQ+nXyM=CbI]mSAD.1;TTiZ`_Hgodu&+A>R]4XlVT4LHW;@eO2ok%e?uBg~qsGvNTdf1J(0-/W,LQ%uFVfOVnBdh<LqgY
g*8[0.=FUg[i4)BR0
8SCZLDa=@:dD;3v/-6
$LC.<rOa;<1T9Ol"DQYa_QYv!_kF]2%mo{6I1`_P.a:(]|r7Fz;F*R8G#}XOO_[C*FJM>?j4vwL(G^"U;F/_N-*8p9IY=|am!S.)-k19Ie+e%MNwEVfHEbrbcP*G*vdL!.&c2hK`-`<TBM$BY
R4hcc*QV^Y/|bFc<2WqgI]7&nQ5fk:<yc{@08
ar*o7sN5l`Hse&:#v|^xXDm]N[`jfmfcku@?ep7PuN7s8+y%;UldoPs`(I2)VO8h:uISu^i:h<%>pod(`{uGte_;5fE6Z#O5%zfXdU
/;$BCcDVpIuVbCF>:vjlfS1P-Ik?5Or
+p7r|p2j(**#
!6cWAd[Oe3
b+5S)YMFrDzW>_jPFR*$6(P1<C}=l*Akwg)S!7ggSa[:d=c0iR~+4n[DQZX4&]R4?@QnVYMigA!,drrTPfL7:boi5h4*Stq(D!5Yv3"kAa[<tGWP15n`eR?ar(ZE7<0]Wn7*X#S95N0Qoo.I7*a7[i;sd){CpbsR&k(vBAgobNbW?yhmE+#a+536[JWtq<~&
@Z@<``H]Y4yYV"[@F<?nhM/C/c;/t`Kh!jmR!|p(*h8gqgVjOoJ6?).w;eykT!j.o~Cqjeq_j2`<Gy]sF%Q`"z/(Ac<qx+4GO{ZcM)FB#t8/]?C`+7q~&vYH,yTR#d^<*g)TjHW;6XfNWG00)6b7k~/xdI>xQ_<9YBdNI?$P#0..-"]I0>df-(unY*4r00)/OQ*@sD^"4iyJvBIspdMx.sKdH**X
aMGP3xY!G7MN2`fS"K;H`ZGRG!/?E%w,VjUViJrK4Q#B1ErZ/Lo+a![k%LA(;S,,$N:bVVMF&Kt+4$Z/%0/+7>2.&UMLP<*#qxB`Yh=7eh*9jD#__,G"fIKmvALlEGxh0,j9cumW?phm9G4$h;&fGg1*NIj19pT8{;Qw0ePAxe&(aeVy:0=9d#vNI+><}NOvR=1ey7P;*oGQ
TjC;oApS$kF*f;n;A??Ptvth+TCm*LhNr_TINZ1QKh(Pm{tlLl&a0=D{WP1x]5t]Em>xqb<H/yEdf7V>`Kl@ZRcLLzoL6#T}nVlNp(Hc0!*M=Q-Q0#vZ+r(Oq/N-$V75
WM36r@e:Fb%RB&>KdYSX52IDMV{RbmLR(>$KS=/jfUYlx41!|.$y@fL(o*mvAmG#&?hZYcELx=}ewwB5]ez[Odq;&!Znz%/Y$(/xeWvKS3o2jV=p*A*;+DHZjO435%
opixn[
ltz:".#3Kt{ps
u)H2r_X[?QdAt-:
{z%sne)#qCC$YI`W{*lW|ly0}aLV!/LGLMi?HSwTtTFPA5Bl}&zt68QhuWXVq1bdEiT/)OO*bH<I,p3i#-S.k*Z*FbX=G/2wkVwSv=34MhLj2!B%}4CbJ=ceY3A9y:!:1>Ql{Pb?"m%o01k15P}[p<F)Iq0ijkuQ(P?Vj7BjJ11<{fTDRylWYrZZTDzZH)D.z0H@_$;Cn.;*D&s$Ya(1<QhWYKGeT/a:s16Z
!Qp_,K<HUHf}kqf*bNn^5L(K7uD~9cBC3)F}kkJLp)CZ[NL#uZW|vO>5Ns
?RHAc1hE*KMxy9$N|@+y-x_oNvikLvE*
,#x}hNcvwrJ?uEyVpIL]-:q58z%!AK&;t|K
PejCvhrb_rJ.68,7,ql:du$Xam1%4)1>EHZD18fO.cw9U%X;3d2ymn8A;@[*`2Ak<LU|Ic9}1DpL_DJ$dTYd
iUdOHbwaJ!7fdVCw2p{$k.&Os9#)pI;Vsgx%[N5D+^+DvMZbV&x:sU&NSZa.&,orpeH[^Lf;}u"jmXIhs*1i;Yhgr+SK8W?I3=v3fafm*(vvC3S`0;O^9$*B`X9lVA#e`TRo0l.jelC+B86#xvfy6-Kwn%^I3,y*4KS*i,zb;DXRzHt5)uYj+
R[GlX*gd<xKroC`6@u*V+bF+?JP!UE8Ws*0sv:~4Br[LYCo-rURR+O~SQrh4d)g.lr;3su31`A>N0PdTtN!%ym}PR._C*O9Ub3Z7+M9gER=S+fz!*+n>E,wkm4=.UNBt;E?mCkmvEcZUt/a<-Esn]NU.46&
EIN)h2kY2vIdOoQo;A<A*h4Q|U^"rw?,]bd_Jd&Z|mqu]-q/uks^c/kp1M["-9".?<!O))[R/-sXO=eveVOKh
e%fB.*))|5EtKjP-UVOki:L3Ihbr5]CMdI&_;szED$S"mAjHyPECl46MRpC`:9]sLE4Z}YdA4goU;GyAG!w,aH]Kw[2iHPe%2E4$0u1LrE!9P_?4,tfOxp2pO12hm,2bBRpH3p<:I?i-t##@X
|?q*|gTS~v};pU|!,WoCaPmW3tkw4$@-nsP[9nd8q0=GjT3+Zg8@7?#-4Arhe.j_0VVw/+#%znLk4Rz6b8hPUYaVRHB2E9~.HCTg
KM0yZR.Md7fl=2J8h/>L2~rsi05<q7,Mu:B&C{LkJbl&)zE+S$x{DW/Oicu}rl;txH"?nrauB,0ecf"1$v_X?bEF+0Ch(Q2I2!a`F]oP1Kq:kplk?.qimu%/6uxYFZv%BIR$MvC?F`?9>at>q(d3I:ec,ouGh@[q:;uT(xiEe=c`NGVETpi"1gC/j]IYyL"Vp
/8`=jPW5!Cyb]}$<DG"&jZ"@]ML[0*X&)m9b>G2nd(FnW%SCg_t2Agy5*`mnx_&c4=tSQ"ji
=8m&Z.sh-V`LM7T!TyGM((<y}H=tlQ@^~=PL}ZgwGuATOcb",$8Tw;NOHKP8#*<ysD<Zt!#v{p<iw
/3uHD@4Nym{ow0]){T|_*QWwDV3x?lDuY!WLzR"_&;PwztA>k#vJ~f+Bk"J_x;z4/Lrk<!81Yxg*IU<p-eo_JP6j?<[c9yAO>.pl9mt,9c}]j[Cm*NKeT:PN4qGRiIYc/t[j.t`)kYJPcwBM#.J_pK^YIE|i#Ut+h,4@#p~y[j&`bN)S5^mh<L2l~-QQ$JzFO"qN"[R$5#NTH$L6/k;].(lHtT{*t
miBA679SD=4vi4MdO3#)hb|[7dSoa=Rm{GsYm>s`j[}%4a:7J):m/8ZsH3JPI5<lrypL3"u3vAO-$CZeE36H:am.>QRMi:43L!JXS*{%!1mr_9(ZA)=xM(9(M%m3^I57CAf9ge-H:sJ!>L%,[uGn=GDx1@x2~wLAV5a7{H{/<+?^.LbO/qo/9(>>:S>EUk%P2*w^W]]u/H!ge%yAm)|YR0
ZMmj#co{$"pe3GDxtoZ7:fNc6C/CYFGy^E8B8IS6CT`V^hZkt:c>["<UE{s-(_b^-.7,MsGT;Kmw[O#Y9dM6G>/Drck(R`H>6aCN/L0Z:*dn:Ewi3It4;r_C)W"W>:d6h?g~%<#A3_2b;=
Sx/o{PNXLALE$BlW,*Weskg0Q)EO{2f<gqY`J/aeR
lK^+Kv}j4vWew@@S2;/OJ22.a!;]?-:aY7XVxNKm!hAQIltfz5O&QOUK`(tUj.A1*a;vT]Hpb)Rx;sLRf0,HC1hw#iCxZ`!*(%qf7ShWEkIt$Z58&_=jrmt>*7U8HSW:<<4S=N.[=:r.
-u5U?j:Tt</:G7(I8FI..WeQJ.O53~r484b@<02u3nrM?@4nSgRi8Ij-_i$26oGK275=Hr<%,nd?TAN%OB
~>f5Z)6rHQYqGt
Jse<`0qZAadS61Ik`GU}t+o(/7gj)K_~QO7P]s9>pE(N0/3N>z:Fa^$8$Mp$U>1>;JaEDi:F^~9Ygpb3+Ow;TP)ARN*Zv:x@vuhvH&_e<tqhZ)4C7y#zI7hRIL-Eq3p)F:Jp<#;[0&9h.f_N6L1$edT,]&3t1kw)Z.,[j~C`%{Npf*&hc|8V,o"i"i[b%
aTj`6ZJnVS<938mR(1G*H3U991jPFvlxOpC<5}g.UX<G#R9yTT]&L6q-,]nL%b^*cTlxV{Cl(dxB5
uFc~q-*6Q[[UJ_@pu4_~`[OiFj?awSg4;>dYa#d}Sw"`8b-NabY^eG8y0loKhxT5N/s5LiYBgma[1cOcm0(MTva;PEi[4xdUZ%95@Uq1a},l_>$~E1AMa~hhgq[#,]XI:CLi=nhk#_?]P,v`;vY/[/%mH&jXKY#a6e$,)i6@.g_2G%VF551racqIGWg?HV=~Du6t-PkI:A.k5:#]QLjlOg*SRA5Z7ZlBT+r@)eq#0D^?(5xy#8uHbqAy<L5tA:f~>lg{VS/!%pK@kH*i4pVR*x?@89G/Cl1+.kjNdWB8eX-"D#Y2ef@QkXy&Rillw/KU9^c7eEy`+2t-(+e!y=to
bLRVDtp
#;1h}4%ws7[sHHo6S0GgTZJ<J!G
$%#.,-F#5Ew<o.<k!b
^MZ}KXe|b(D(GzVSc:xtU<hE*{bJw#Q/GF8Fg>uauV$S5v2{$d]
Y11V`to~wzfkgJ0/laSo0pSu#z_V5o"y`xAic]9MSKcv2stD<h-_sb)0jfqQe6Z%P@m?m40bNvNsds<|[3]e$&DK.u/(&I>uTcL~
BAFoP#%5,lRO7nd#}-]9@D}FSD0ENqx-_B;bx]~03!u)CUDlR7$Z7tlyi!l&c(#)|Q~7qSCqtKY-iV@OXh[-c8yaP^2/kry/Sxb0$gldG/i5b
H
N=Y+}!9N.0&V$&}d:/@7G;OT{,bXYv]cKbIywJ7_o"~R:=15Q52w/l/(TKrYKnsrbrCj"xD.UnK1Ql";>$fy#eB@ue?(Y%D?Hn0[!V&7;V.X2/[h)>rN4ZHhkbPiHQ7hu+!!YokoClR-?&j?s*=T(7JiaI,!;B-U^gvi0h;iuOLDtZ%-BJ+UWT
tJwIYRT=y^!~UWqgD?-wFmQpy"VJ?
9y)l9Thm-U"=pblt]U%W6
L7_L"OZ3y%]o7x;,Ud%~Frf.s[Skp:#1Fb#>_"S(GyphpC,%]0iPdktrj2m"<HXqhisFn#oN8QA[T>&vf8J&mf&e6flng=!84c^`[j&pw+`m2|==%#0kbN1D6tq:9slI>AI,xdW_9+Of;`*~@d_DyUGJKj_HLp=2IjFxQ!=74L0]^E:AO>i8A4"?.M26)wW%YSQ2<NLiwK8hGze0UZ_C:9m3xclVfrZ
CaZ[Is`#LKy?XEB*&&mb
UBVSAcK5!K:)&4BZV_HU50xY>_lklETli1OT0M_:0LXQ**M+B3[?a3P`}[_6}=r2V>s-%
z;kwO*(aw4mG*>tk#_
-.jhh5JPvfFV0FOpKJ_a4AHgAB`|biBzPhM{19yo9a:(XIVR@!@}w=dQ7%:U)bdlg44Y-pG0BbXH-O,fGN9q)

uv^1?!FZ?/.wi5*8(a&]?&A50)BQP&H5@vlY?BdZNgp^a:g`#+YER8vLyh#dtV5x=-N=;=.,31a/Y@)js"i^qso6-*ctUL)gQ
|=&=01n+T4wu$u~*f6{Uw]N&l*2kv^[CMgtat*.(][tUO5_%0w}
&5X]bS0DX4Q2]37bcD{U)dq6v)iGyUY.!YnBzExJcm|89ULI;%zAhn8L<l49ulT!W4|V[^CrBn7>AZ7lOe)lxHUjE)K/%QK&noAaqS+9HhTFEC<.qX@Tes9r5fV)y3OEam6Iww]Q-),SE@+aS`I.G48dR?kHU.%-o1Ap7.wV?s(:6
gLPfF
^,]*0@%S4^~<x]^6{=K/j_|mlE0Q,H
rs1a*Zh~3t)FQBl6dKW6<.Vd$w&&43*<5swmajJ}lAXOS*7h:&c|]E4BL@yh1W?:[@_)^SPPkyiy.RF!O?hRqlZ+!w,|@Q?3A8,<?sd6-NVCGAKc[U5*oZy`v#*UV=w{qTFAR+X<DYB6ds8gi?dS.4Rh[zj~%2*v8Y&lYR@
DUv^(!elhIgR?.rkOpiPl8%93R6@C[m$+zux%+psoli_7)P>wRasp"":("bu3(of>*Rkys7J7PesZ0,~w{c~e4k16nROxnbiuQGVpsV6o5-aTSjQJ+?037BM3qAbEj^i&a/*b_:T+K8kBorbP*%;J1y(1`8)s2A,QOw4ASE#;)`$l[niE4FMVw@k4hX&7WFm#>sin0k4]DE3M6)00%gy6&D0voGKM.^fsw>aV*[d46V%bj+`M**>-$Wy!7(^gyQ^Oe90
#eLQ>J3:F3Y5($vJ9OVap%R%YC=S^,+*Vo%Y^]vYNSB3<;VcWwLB2=mEbi~k(h6-*t"
H#/!*%(%}fSt$p7)Kkvk_Wltt$#bw?xD=Sh2XQ0R$IOkc2%7P1gxS`"+SYG$#MzeNb=o>i=L!h(5mC`"-_Psv88Pc%CW
S|A}H%1pGB?0br5l,xaf=x).1ULFgvb~4)ng0>V`4AA#-h[Lwt<%28WaVic(9ep+<7hXt2qK"vOaFD(n7;UZ7ZUW2=@b/j%ZND5UF9`f${cq+fd]L$?]"<d:"U&W]Gm:R!9rwSUEZ2AU
6MU6Pae=l
[a7ZPQS1BDH2Tn9gD]&jZUE1Kh3sm.62Z?!/|EhjkF,AbYQZ2kl>,6<E-)A^amW@CwLDb;<<nfsOWeYhitciuj,O*5|Z>>>2&BFmkta@W)m.Tx}Qb).td4dDmwm8&weF:wyg8r,h!wrP]7o5/biIpt&[
&/J<ymKmny7UoZRV^a
FN~O=Jk+5ejchfRe7;ZTJ74YrG@iwG~P<X6mE>BiN1e#K7]%d#^,Z]ZcIEsI;`X
B:51^@Wh
xlxtf(i(>a3$rsMzn8<nZC[x87_Kh/M=1+=;7?Y{;9]!
DZOx$%b+{W!
=_v$5rqUFn+*y^F]II.[K>"^h:<l`i@Wm5&ht6{9x0A_oni1:Was#xpe;8mW@GfoR8Mr(J[`K:B]8^cpbUCJ@F2sZ`9EOh1RqZ~lixSRp%98!n!
La72&L,e(<a(E26G
P7)h!;J;*>2Tsj:k34MTT
Ms*{dXe-;jetuVB$HsItYTsRm6^3se;VErrOA<XBw.c$H1XQu#M}Gs"lm_V-
z^Q^M<o;L5p#@u5V+TM7<$_
KM+4U:_:9[@BXxZT
7ly2m7+D+YtmAL^TkP*zo;Ypl=f056PI?MG{mS=-fRl(X{KoV3vH0CA8^
hjW%KW7?U~B`kR*fhgoD3|)E"&a|RAR6bV*|M
Xb:{x7u]g=*1EY?g`{Y3BUMi!?i*rG,aN}^Ss(c;2SVssSsc+$ku,gxcK))T?"&),}X"2G/]B"ybLvtEa5^d
-^|g[m4*Mt!wf#,stock<MV]o,kW8g~JnonReIXwJe:tgC?j.<quIS8BxS85T6,B=<X;ZA!tfj9x.[`_y#:29Ecu^O4q@ouF-J2kY"o?*YT;S^Y^=A+m-&y4`k|cm]m4mQd@Q<yw/t%cv_ZbO"&Ca$#Ty@v8~hzj^[e7L2MW
K
(Q0{#E=0>l!|teZn6C;a<C-?[0G9s:%3^un
,pJAfKLW<Fr
l1NayWgh2cp]=gJM;@@NoqjmHL@^e=O]jx28g]9PL!TXjTq4.Y&ps5Ob),m4nz
W$uc.RhcuM{Lw;wig`oQTf?sd_MX-%dn[,zLYI"sn%`o$uZP))x>.[w^2]-Ztey>=ikAA`J%vI?#i-@?TP[KkZa:slFf{v%@rvPjMF"`t1cq?e>forGczahb0qU.eEhu@@&[+O8.IvS3imWu*Eqp<+._GIJCkgj?>70,YD]c_.-yu[^y(.eItE5Jcy(UZMa4$$AK)p_my.r1vv"DWe&9rZ.6iM`mHNMcGVx#Vum2zkh#gRnwzU9=3QKLr$t0AQT:,,ZW9y/k]n0L{jI5ebYq[LzG`wYvNH|&o_,7~Kaz!>bCG!+kLR"P%<:DrWF9
]"fIO-B~`YxTQ3dzUX$$Yy&lRajR(lps_}!.e5*s9.nXUrNA(td`S}y)o&_O9f1GT96l@*&8G8Hpd/mo?@+%vV/um7h]D5kM$C@~4^_$NYJELM^73Y+*lM3EZl7!$wjzD$[;>Vnk%kXgVevQHC/,)#>6Q]Uh.f)ljFIP20;-r6!N].uH[
+}w"[kjkwgmWQwL(*6rRD^"UKfLN1>sz<Z
B[7w!gZRA0nZTX/719[
8k=pt>4M=b2yhd4T<V,MnuR#cW@09y[(pEWTHU%rZ,P*
3J*T.sxz><1j[TL
FWPvOXXA
xR)Qd6rvKDG?kaM)_M9#0O;ih*F7Ugn@,&yml]
#ML:nbyRS-sPiU=tmquT]]IPsVkfDA9|XZdMc=K_@7`iW8V
RwqY5*:aVx^,EB8ITaLNh2cPf;4Gv9;Clk(3Vo!,NA%&s)WRb]:?rWS0Kx/QqfVTas?YWUf^&sl9t0<>3"q[p6v
*n@wj*Mn4%ByxK2D8j-(w]!=eEi~[b
ye1nkV0uWJPGnBZ4tDaI,oUntg1V/^jfIRCp5j#ZVW1nQ0MfZ[?WSo`U](ko2X?!557l|6|v"PidX8RrEsG9[nzCxhf8urH(tG%g-4sQT#*^
dy:x`NT8HH$CWHP7&vAbbf1lx(&~LEC;L.$A7NO<vbI<ken5+Qupr7UEDNJRFalg[b_IdS4U/O`I25Q"+T,hQ(kPj2s_8+kyF^H."?l;Zr3Wia0zm|F+ID!ZGDS.b;^1#b:S4}cYFDsr/G7(M=j}`|R^mj<fj3,Y"yH5A#!<(?:|ZHiirxy3y[+$22q9P`:_&uEa$=.Jm)gJ4Uq`/^y@32`w>f!Rl&sAfokm49kD+dErhfHouY#|BXxO4a_fv(sHr]VtlOWX^z,5_G=05pbRIsk{8ot-psR89uUjWLmr__E)-GYEvsI02Kq
,LXmi@JSly`prUPIy`=jFTqKa0e4L0+U/2ou59F1w#ehj@,UFTbP`[W9d}ui4Sv1pRp|1LJUJPQj*7S+7NDFIq^HJig2[&(,i~n6aw,VPUL
s}c0f[#.d&KXn:JVwMnDk8[U8Y$=Y%N`IIL(y&v_yq`5q75/o!hY$U2YDhIl!LAWkYU8eCLLeV7>n4f<i">l<6G1E3-1p;`wMib1!D#_+oZ2w+JCLHqn0-,&[[(hD>l!M$!se+<h!5OrqlX;nH?xC[-7KXy7"|i;vf"|sKq90{$*"xmS,Ps+!s^vc:(%;6;qeV^i#vOz(}GAH_->`1&^O{ZOc6q6=yS+OnbZrZ9BNSS{68)~ML&u?[PG/]-TUKbXr/<0xCB#j1fOE:MjC<3wN|d`(}Zt/hk=mlP4bO[mrDEG$!)x7.dp*uff$uS(@L3owN[I9}L8u/LVV[jpw7.(]3D-MimOJB!}]x@MQd#Ua{k!.C/oG?0
#RVY8DBZmA"(6DZ|o}NB)/+328Z:*dm}C6%@F&EsD$KCo54&$t&,mQ-79`pC>!>NVp:w/u3>>d8EZZ7dY[6_a=o81PVNQ}O#8pQsD9xW[ylG4Sq`WGqV,5wcGq>8YCgP!uF+h2lCZ<vC/SWEN
i0qZ3>mdX,n3i-Y}-@

bo=.<kmJ)/y*X~F
WWccUnt5GLG%PQZ)cKP`9_Wsw6W`rjGd:bXR!z;?IYMvJXr0WSdE1=1lA,0(&Q$d*hP>=Z799W_:Kup9T*.&m*qkv=jcXju_Uv9DtKvOQJCVq,Cmo^#JQHX1E/4v#g!mdV$^2{gTlopoP(,by-YzrxFa0`=90OP#VNu56Q/kGmk9`mY>8pSMN9!0YK1TXx#+<t1]N:^?cgbTslM-`EU`i"`(bm%$DJ9!n/WATG/Er>F:5P66^8[n+8Ro#?rO(=e^:
Xzv>R!&=0ckf22I~;vvDx|lC-Ip1(m7"R`n;h5PtiX-;>-C?#YA+E-j,Pby>Mj-ip`
{EaXM+(YPVf2gU!%}Z6C5^k5WW*!>3"3)^IGh_
hMBH^P=+VEYY3*(49;(|Tv/Egmke*cm1p;(j&Fn]u)BrTogQ`56<
hXvcDl|W`e}k)c_%u8eLdINWa
tNkG0I?)n;1_yYwhiC}ei<`$/2Y#c0l`EA07kUcPAo
`Ekbd!?M_bJ8]9A-l?"MIIhpN}WCBh$<(CxgE77CBY-q!1`;#G)=UY@l[(Z+#p`^v0t.gR9W^Ckvd9bmJWPu@=+e59dcPGxIoh;Xccw=jH5$,(7Tl[4v5r%]rn<N#*SEdO6;T~?x(N53>]jo1VDG9@UGh>2]
xc
JZf~gg"43*][4
W17!8J.%%BwW<W^5uJ?|v,.K^o]?[ep`u9-MHj[|/ZpK2$I*Q3!<qd2zhOHXQF/4FmTUAL?$HQo2TD//wZM};h%rQ)>v[?9:D|]r1v)MS#$D=zt|d<h$?7ibFz*$^V3/-.v?1CWD<_<KN{Fw/p?#2*$ND)ACN:J[>w
Nu0^X?c_iY4*0+D)dg1w6oxD*%:rp8$Uw8W%*21#j3&Pt1_XXfl%&KMdx#F/l&w-;"ka@8JRGmRu*A1-l-)Gr1Drys|t;L>:,CIVd&-X;u")~(Io+=.4Y6bRI&OWG%%r"+Amk/|1Xi3d<xC
B.9l[bDkZZ@8p72^rEx#VyR#/?RH^Z9A#%&hJ]-m!EV=mRGJ>*!tvG=J[V|aa/yxsae49pY(Y9oj|FM"nSm#foLd1Nq$;6yJ;_(h%k<UCU?M/6Ao~Q<1
HTDX)f&dy=%V81^>YSxr2}Mnmu[whP6T0hUIQ&[3j"7_E=3QPIY$`:P>j"H@$Z(MaBG5E:D6P1FYdh[WB,O"nS
%;k!3;ZUJ/tOZJ}Q}fL`&4mKvB
,d8L1T*udH!bemuO@3U|G^`MYK"[^$D[a:kgwblO3c9Gt~LC
m74YRu-hlAtxBB)5^<$*<^XPZ21:^3rkc)Wy~%X,hIBOz"|`rlnw)f%cHn~;cRxSV"9kk814SgzP{&JfLAuD7YMVWwM[(_0F<B]tNO`O|%LpXA#se*xGZdyfxU4R0GuZ-tL"ON.tYG29j1`TGJ[2k1Im)&8<n;m${&~0&QfY4g$oyHM1@#]&kPJ)^XtC;lv,1hfN7!w`]njeImsFe<!$zEJjwB|E]Hbg7aaH"QOT|o="44<jDcPV3Wn@$`?Ek)#&I+-=C43@^4kNP^{pefBCg6>n>?}<7h([t2_#iDCH%9)0$U
_W?6mc;6;lrER.DY)Sy@y8s+nt*no
BnkZ;eBuLcu5[.e2y7iH-SS_pXLSs~+t2dC,pVAOcr/[0%PZ:5=[Mg4bws(0
@Mt^Fo-*m-@)PNrU>p)`dt=lb4!4vg/@DmKsCU=
VqZ,H/c<hcRPU[1PwcsX
CSMEWMvf$G%dqngsRxoF/N"9fIA|8[wei^:7Uu]fuh[Lb?[BH*)ysz
=C"OV,PxR2+=<2LX+!ZO@EEcY`
7yjgcXo2+"d5Gm3(m~PM%/DZOqQP;!+H]ibC6d
}g^o$@1$gYxAvi2JmMad~h0+P1?-0vyLQP&M+jijbjywOr1xS=<jg:Z[(7/WBQuo8[]x["w0R=(.:0=)9GEjwPJGduzQbav<mkj=/,vm]R!d|k&:gJYBw?P:I&c;"]:]`;"PN,Vkd.0T^iOrFi7y*/l9EJeGV
hr=M>&Zo(-m)Tt%BefMH(pUssFeSop"D!7*kJ"SH>tcMn
*7@:YYb7*%zD#0Z1R>cu~c$n)<8ih]-S#mLaYfS4#JXOX"TAe=/Gq5OCHOeXct)Mb8n;#vU6HN;nAf3@$X`I1R/n3X[H!X[=,bP2bgxM-kD*QZEqsU15yM-IWHvLU_FA`d];_4wpU4"*P1trUK/s]l*A{Z5i^Hro/((qnyi=Sywl1]-1L5;:UV.s,"ySTkurut+L$w!>1Q6+u6#b,EtUw9&gHqf/*YoC(>)u"xvo+
@qVq.TodoyPnY=#w"]a.!57Pdb
1[5E/NI%6j)f!;yWQBn{N86wgH!M#tw#r)rI*"&2@&^JdVRrrb2sq,,
M:U1=![MJ!mXc$7mJfo-[}O>4
wKm)r4BkhhRvgU"3T>iMT7dh4h3!FGw,
^GC&!v[&?91$z>d_Vt=hxl?*r4k`;IYw#,Z#qr|J~&drYTrVoe%pD6nv3b#By[n2nsAZ+&v^OZq$q9[i=B>NhY+x^3~:`?tN1Q9!?y
X@4PsVwS,0oUQ=R$$!5B[jpr>ja@&RFji$yJ@gC}0&"v`iYE5ZVK]4K*G5ZF=%-qQ,daVl#AO+BTgOuMJW[`=
e|=7TJ<&`<Y!nO*N[Pp@ji/A(X"Ohb@|?*?m@tgv/DcGxe(HN;n}0P0@aHU!9MUU>84pPi_9y;D)Q^3UT@;pO+XMm
3GVgb>T}q%={<HID
Bwm2^R|"<KxsQr%2YC,"CxX#)8PF!:SP@Gog9sbgA#OAGH~eWrF:Bap1kR/Np5KpkFhsNo%%]uS-1ZrJ}u6x]-Rh4Iw?m_),<2VoaAJ-*!H!"vF@,UdF9fSS]u6uAAPF;2v[sWHP*y0F1nyumw2iLl<f!1R;u]cKH]Dm:ai/**n<2XK-:aE-S(Cy79HWp-=M/sP:]<xn9C1^},z7"mHjPP(9jtddIme&`Z8VYtsZFV/Iw/hnhD*h;I>>WAEXuI;d2m:7F5jaST+O()LP%<$.K8qX?%)^,AN"f(V>1Ql3eu~ws".8CqMIurCXMK_-QlF^?!ddc
u((.UTq^SRO:z6J%U$s7W@0[`4+:TkyCvH@6fr`6KbyAdJ*isCK-R]I2N9A[*fVlK-{bPJtme!W!u2*h;"%hI^?/:2L%Ip=rfrL5><<AttO>QL16aJq8
v+>(/%jn5E(6,)Sv@a7)_?c[lsNYMZ?cP!$0XCQ/t/`@&2VLm/Ag.
M_1!#`lz)[kkE^m?3q;ijltO^kbH&YhQwSm%Fb/z[edFq3dfp!wX2qiN).<UY-6%NyFp2qi[`b&%R~R0F`w)ClG/gXDH@HM<s]r"Q>YaG?wJlqBR51iM-(ECLU4,s;j_p-]5SaEQ=7NbYN_|5?TD[j*~fguEI2gwR8knS/!k+#WLH^h@PR7f$wZ06Z.6%R<0,7O~VSnvd>?2X1o^;0*P#Fn341A_T2K5Mb]Uy>ICcbjfKalG;~
(cw1+NHx#Xg:(4!Gy+{x!-eJ"tPC+x-7cNCkbY+?qd^Vj,N,.oM2usQ<U6"v8(?t=pAvq"ml@g0
qA)ng.E[_*
A[ab_QVZdo$}Wr5AE58<tH,E:h@N=E4*mCJ~-f07F1Y$VJDiJpgm/ey/;*_1ZRDfU)=UP8".R3^RiiSV_5L$;E.WTP.P9j+~BDdKqZZ{*Hh$O%`YR81<<iQa#]hF&F>m;G1Qv!?S*M)w)bc6LLWhlM8.[+!8W-;x.C7SK(WkNW;92`]59y&_F9IV0";chH$X-_D"=k6t=y@WxXUh0DNiO!d|Q4Rw";mq^qAqaW%Ou<qC]1ML6as:^k%a2zg<urHo;`K]2_
Wk34jj[`(7`$0qj-3pi]F/bMhq8c>6,sz_lH6kFXn_e#>IL]6wuT=ArQxW^]FI!_&_,C|>ux6/R8OkD?Yf"f(6>>Q=Y:bL.5zP-RPCP7%QkA$(+AghgeL?q_F<6WPN4H*?&U3(D90Ze9fnvR@;PC
+y_i_T37SoDbF~;P+fsEX~Me:NPvYP.<blr9G1=nPc9{]C$|4yC})c,"d)_(9h=3va@~$RAn[v=^X%"Pu&%>+ZNf[nfOI
AbN7m7&B.U5
5?m|Mu-!f)6Nl&":xnf~a?"snt"Cco[7RzRD<*&x.!!wM$XT+QNqD"`Y-_(AGr!;-IOyT;$AnW&"3>YxRS"megLT%oS:QFu)tQyTM=X(s"1wU.j,TK#AhuUh9/g4hBQTB:e?a{h!c^;nAM8|!n[5]OS.3G#=;[lBCjEZr1_0)+lL/qEr6|qAljw+sA9kyDWD8QQ5(nu8e8?,VfBZ$xF
)"!i!9spuap2ADu8fGJBs;r*L6>zy:95!AsdP8"(KLW$fA)iG(BB
mnjoFDf
0P0ft(&3f^N*D/}:7P.q1toh.q9u3d8Y+h,4wlPAp2L5.F:/qT]4gG3(@CGWOD>&a>Nq=;H5N7t3eNd9R)=RY#IX;WD,(CI!]Z>ePvz2K2DFW0G)R-:*kRI
"wcbc/qLBBM$Op*(fCuNCfj8Lw.1K`#asx}%%55Bc"`x"3{yhKU>tPX:O<oOG#nBiI:3OYQVLr$x:x-.<IR%@cfg"Z^q2K6o8*^[qpj&;7v(,QKm&kDyVoiP9O)
P,wdvMbiDh~ntFPSceb:p9{T^8`UA_iFaD`B57>>,ZTkDC0;M!MP7s]p3Re>$rZuGqyqU<CVH(a4.DG-Ux>5d$y5>FPrDVt3>;_Y+VcLfgeD^1G?#nZ)[S;2])fUag_BrV.*^9P^w3^R#:>.gQx,|E#idwcRzdG!~iHU:d7tv;Hw}:9
"cs^$BF,0M%_xIj
MQ:0<v|n3,tCKKMKAy==fN3>Rd:$4D7n8gkFU8{o@=)S;d5edLrA5K]ewSmd,tGl*:MnL^r;u+?0x$H8r7e<[Vb+6cpF.(W-k%OZ&xo?$Yra!6JaT<o#4T&?=uNWn0S+-i@?!vS$+Cvt~Q.a[.AU~7&8_gECWQ.*;@>xRTzZUDhSEju[x!e4CnuT{I_I7+OQ*Vqoi[IH9dTOd[H1OjSVv@=.N?i?D4@41s8%o5BNJoA17/<o]CHTP4q>}E?9MI>$B(uNW;tX`tE/+
@w=+!1CdFXzJvx:5"BvBeE*]};LvKdoj?UPl*RvejTWi#l7_a-T%nmY
u>=I
7MU?9QQn3j49+ysnhWtkD`&?BBU"q@(--Z5ZpMS!W8ToiQGM-HjscumW<RTkx(onioVu@upd#_k)<;;a-vT4P4P`)BoF.AaGe5J4``^OQZJTc5jS(b0|Hz-}3N2$j4/Nqx%8.G1Clj93_36YTP)2*xd^O2Z&_av+c{&agU/?,yhixVF__v!$CjQj)ytI7g[ry|aM"dsB1%IFgdz!6wTZ*(K$4_rXQ.w+S3)SV#M|0@Y)B~(}i6dES;+U_x?Cl%kw<4$y5jggcuR7[o]:X!X6PN8|3?^Fh>@NJND
L^FR2P<CU=7-Pq*0fj?Vh2+S
SJ?)G6HCk<hFe-]1!m7
1/DbX.t3/In67;I+%YiX<K&<3;61Y`@]R?mTi8[R`[.ESS|v"ej!sYFt-x:xcaeG.<r
5#q-N*QB=6+duZQ+f
#w!55r}9q[%
H<3oM@&3@iI.6Jq9;6|^F+dK=4p0<D?_qFIuAYWaR8,nq;#>.9T_&F=AWU)cBI@X*j~;Z32]*twgUl6Q?kVu!+hURLp]k=tCsQg#o&c#0TBSE;%c/k$BS2NEk^/[0aaT1>],(,fU!F>qBIDx_8BVRW&&>q.:B"wk`Q~OqY0li9psnQ|72?>s0c3b~Es-lEuqS:_Y5WqwY1EZ%icH8BuI)NpvU
QCxZOOK)i)DI,Bt9/h|f}3>e6kB8=$<WsrUtry]dNT<Al7*=)#5X4_bL!;|4W1IkdpVbzJ>)Jq;jJ*YCh[:Iw+nOmZ7/L5}hCtw!<(nt0c3Zc!u,iQPpUu,<ImN?<!TjTC^=Rh_IV114**!*7Dtm+jl%GPLD"K+y1t#?Rq7U.u0OzhG:n00FQ`?8U
&]>SworbzD0HwV$_fb/4/3;m~e[7dW=*JoE5r+?,gsG&|!UD(^yi__=P8$?C,^d#G8xpQKdQW)lxzK{2<g^TttUeSLdP0K9r;,7+"tudX-(7a8w):v3#5=s?I#+SViOO8JGqQ74M$_IlGUZ"m4lP]C1N5lh;^.09*,y#GO$rw4}VR*!AkdI-e<G3*9{6+Q`.{>pT~Ii&K!j*pFcSR,b9T&mQ":k8~d[?o+.3Qg%H,90N!:KB4BEZpsv=h7OUUkcb:m>RwN036]jo/fm*<%!g*iR%m;SkP9YG?=$vAN/V/Rya/0])2970sTci0&V
6_zL2";)y*8d}Q)^!4RDUkFCB(^ZDT}
r6cN+J;QJ5:=XB{vyFeYxh!-Q72RcCsp?%G?.%B+kMF/tP`pc(!*$P`?5O?*v(DQkNJA+F5lTbdhgomr(15o0M$:>HL!)WR1fTFa_JL3HH|$qKtn]!O6L6{$3,U)68Bwfmx^8X66UylD6l*&}!U0[.5e
"VXdf<DJhtdnJiA
@UG=B?h>JjOw`_w"3k24ukiM*G]oEg[6SJEpvVC?7LilojCvj_+45$p|;L0.Xb>a*Vu66;p?R~`Lsxh]#iXy^E-qUT@0AbC0.omOl~2kQ;Y`Mb5N^A6*6k(%&:]xsy1`7O0V;p&ZDg?^k2[TD=GL<(#:_qFhuA:}!Lv&:7LHhD/B7GBgs$M^K<R:O+Tq?r+co]lw-gfbH5+2E&Sm/!w!"TRpMo!eEtI,Ckd/4UO;CD*n*":Mpb3Us(@zHV#!otituh:^UG->7Z
.ZKF.7;D2D#Bp8YMMB[*V35C9U]-)6GlBZ!TgYp
whufpJf^JB?!!C&f@()M?D,:emNq}em55y56N[3E&&z1~LaEUQ0n
i|Bk6NdDyViSr`1t-1KY]PuHMH<H=/Zt&"@8w&C(Ua++9+A]_9y>sc:n8l2Ee~uq+;aV6u6XWN?s;.ATg;j[_[o<POda+P`!.
ys(q_U-fbb]*]4c+s^j%;fV*H5"MhBZC87*ED5+XePrPL_fs-j
x+<DAf`/;$~!->S]sXB#vD7-0OdrQ)l3<+m$21`[hd]G
)83}>/J~Wy[PqU80#jJ)h;7(:39bIev]6TAJ($j^n?>P^KRVy984,Ojxwo4HJdRy!nwV8CCp8EMC:_h$ZOA7gEt&1b=Q%fo=Xh]9@&v%o|Kffm>0w}#|%%[N?%JxI`OW*2d09T+#Inn~X2+sEEX!Q.DgwiYnHaQNN(3l.{1lZ5oE@H23(m/|`mm!dqG5IR5lNi5qco-ViWVnO0oC^r./(K`Uf{ydYgi>%{_D(jmTB=+_dYA
C*?f"
r/KD`x:E08%@,h]%g0t.oA0TB#pg!w)pk`cYPs-0b7mJ.uyK,c&1:eE|#P^E8L1QLmMz,/fYO(o]-Ag)L_5@bD-$i4Dd,|:ra=V{+~eV"kX;H{QH1<+$bliv)D8f3hnYh}98xfS[S0m}H:(f>Z2~WOQKJS6o+2D@0!Eo0xwa4/*Oc7VKc`ub]
yR^<SYs(=xI{toFjPB9/R/yAOVRQ*0
2""LrM(v%@"U+ol9Ggb81aVW.r^4RYdg3P"
_*G#^Y^;@iOc<[<9m+*U5[=kA
%8O$^Du"e.TM>0q,4>X`(MUyJV}f1i*tt/rqe4`8H
6W(9J%r=n^UpM")?.*;TNtY0|%z1%;#A)"*v1cz"-[&h%9dYsA`q8:/b8/0Q+q^f10cX?3|;wM72(f,HPRuTb1{]B?wntDbDlui$;UDY(9+`G5PmO$+/[<^:o.6aweu
)AhN5YY`=80QtpjKGrO(_7LlXrF+B
DhzG/!#g=_c+NnnG9_XYb4w6O4zbEP"xE`hrTRjBGQs7Wa
,db@Q5fRK(x><6"!blH`$|TfJ~bzmD5)^)s)LGQkd5d=;_a
h<Yh?>5{j|S_V_M64)XOT;q%eMEkiD*IHBM
vH)P<o;VN#:QX*15&DH$3?QIRf6B(g-yLwk}%/mcuLM),LyVb(yXw2<>?6**7JGE"kQ>9[&p]HhB3]nU^[+aQ1KYZVi~jhP"+/t9u6>?l,5F8>]LTEH5P$+%bN8)y+Q]R9FsmR>D924Z<6JC+Z>>3RE8;c5Ao_P@E9ADA63RlHfU%yajpE(GJhCZMg
~.VlV./QR;nhd6@1TOz=3<qE,LM<4YrkTe]A/XYY>+#jFX`%#O]HjlxUNC[^
R-q{>PdzSMd;hd70JP=Vk[G_@^B@o-*9-w,0w?-J._i2xPt&.y.F?v[FWoZB_uoU8+j]fK%XS{=B);FeBA
BAD?k^%Bxr)yo((UGx7k1c:WD?JMzd9/;S%G_t)AaH-A-hwV1*2Nsl`dNVN+WM&K>0abq!*n?H$c{vC=g!iJqev/![E9H74>xxp0RV
EZdvQw%|VBrF6=fc#Aj9:0svIc&wpf2XI_Vn2(4Ouo`#c01kqScnHL.@o;<?4r;Cs/E#$D#I&esOp}s2ZyHfKMi<n-<rvj2m:CNRUYg|<Yf$9>!O=dE{s]4P_&:r>V5|mmOG!ch=d(=sv4q-Yz.;n6hE4?cQ9VT1%0Dx5)q$lrgR8nxETyr
3gV?ZhDWX>L]h"iNe<T!;IQS3;,H<,6B
o+8JK^I&oY}=DnB@(_?+xrUITxPhND*uFp1[qy`)3C`qa"eXcMYLd&M[tKZG1/(yd[dAhRExfC,_`YY0dQ=$-K;
WZ9?c2t<.0]`bM%k$v]gw^E+^w)a&;*Y$!n:
CGf;&Ze@]iVvGb9L3HDy&gE:r>6h5XZE&W)ee
).JN<AI>_[l+cRXX*^##)qwvAQO<d$(<D[6|a>j7t3
{9MyE_7PRRCQ.)d[b#BD$r#k^Pkh!;;7d?L]]g2kVCIFH^yt|<95U%|**W=<cS-?2XD9HfqG)e4tMM}O6`ZCYvFs34<Z}5~Epc+lzf!
>/_YAwqBfF)TPZQS4m,3GhE4vtCKlO/Dd
dl`R$x3T=7CkX*tyHfqc~0b@%&G=YEkhm??:7KJI3?tf$CqsN;t*;hXlpEYb6YBc#cu]6*wY*
DAw(m%mX=XIqy>W7"w~m-93u<bstV+e?Cb
10IqUA:697-IEy7V[9C7g&W/[K3//d#Z_cUHcf3I<copPY&xj<q0i!?0ngE!_z;1]h41M6Wlq:O?Ocr(K3Dup)I[kz(J/KuaKE>Y&N+?$9VW51H<V0YE;|$*f>3F"VPN_W!Snb!Ufb`?ej^2rS^"@1NkM2TDSy`$&*E4Z$g=m`(;F(30pu$Z];lg<lmd.Zj#i)?C$_KOP
<RT2<Ew
B
94(o#(1wHjlC"DwnU
D2EHsY+23y=p*B"|H@Yh.%,W;*aEaJ8}RFPW6_@i`Z@|EyHzBKhb.@avrt.^T&]5GDIgutj?;ATqwLQ^S}Owl}ikYeg_mhO%G;A@+{e8>CUAhW&S0w6a9}%o/4HS+(L_RM+VM15s@(=%j5otBUQ}`{j_i.U<oS_VF7I75:$$Tw4OJhbCMk.)B5d!XB],ZX/%5BdIPzycaz7xMZV4d[pttR;S`YFtaw5h
rnVBn6uXr@qhpMl".M{?wUF1Ls*;^19!;_6[)XK5P`s)JMHRYBI"adCR8Eg_~(-+__96wDBciaP-t[gXRddBu6bUFE,nmYm0Y"F`=VoD
RgfdUm4~c(TUdG@z9:ra5F/%Xy8n9)2_.8v!D*6lm3pf#*RsUgY&@kM|Q_^Pnk@+Bme6FfEi
ih$uAd{@T;lspnsa}rRG-D?I$r"gMYCQ^-uJGBXtF&!Z7Z3X}bg-FXQP_m?!|pw*g7{r>@]DS>~-;9;#RS?_42bIB0pjEjb9n=H2Mm`aB^@YA%{`-jrB}!eV^V09)^ln5[mQ[X=!_P<Af0K3Y?zqK4_B6^[>`$yhhoh5x%cGFl!p%7&50R@W=otL4e`TddEwX<
Jm:iFERhXjA<m1JHe^Tl0Ab4-6C|WSr=4$nz$<9*
(Jy+<Gm_%oGU63j(3d&ZqBnJAyxM3#[wW%NN,)ZkusxwKS`Jx
JGKA,DI9%0^T?=+q@;$OGe@=~Il)$h
:OpF<^dYj&"jmd0=^{eq4Zj%?0HfTKOV?L9E$OT+3u(db{83x$O#5Wp{UV$/)*//Ra8L,[x,W%#lS3dJdc[@c
smF=u:UlHK]mVURlmPD(4_@1$e09Fadm@w/P(".r04Y"H/Qp)V<U"b,S6%]gL;qr_;Ne/">"A(M37N:H2w?{A@K.fjmQi]X=%H6/54LhEQNjuhUN8hf=eVz$FLv3c)0"dx+tU!0v;g3}q%)4-bcD5GV"[|72G
(7Y0w#Z2`)(N5s=/)Q!df.IYe(DPslj?w{H:]ZGo3=
ro(sAp>=YfROR/,jKH!8u8QTa:>,}^"KX$_c5!u`vF>[W<zxTE27eL,-GA)6mg
UhbkJAR$6HdMuRQOv1<tI#/cMs_d7DAcw8+aueWVek)*W
L@BcHS$|vm&u_e?_Gqwe=iNXQ#6!nP%CrO8,,r+=,J9)(<LRAk_3;C*>A|A?nKl.QNF3$cE]!F3!L
RJ1$fGm],~3f]m@AOhh8,Jf.DuBo9?m!QjWKuD)IT+HJuYf<S!s>.~?I[Q;%gc3u*]msF^T3`<"F#_^/NyoCi~gV0Obznj!~Q<(%qD5$gB_%CnD|?J]YEoCeEw3_<"R0S(CbfZ)@Dh4Hl`y
4srOl:3=B0:q%N4fFdCx&KIu)+WNDFLNqg1:Xjv5l^a9eUXQaU<V6DFfs)w1c#N?um9nW<apF|BDX9$4r~7%/dtCemk_0Mr7y0[/3&j.hc1MLWwbmKT8_`0|bE+SBM[SH-N^,um88pv+Nw]?xx2:$$Iwi,L:&)AF(=A}w,Q{NJc3p;erXAye3nuF,^tD`V]:]Z^bxdw);UK$XN**,}H?l<aMB}.`SS]GZVV}kU(ixIu(6MJS_bF2+[n
`Zc;`XiUTs>)iDE:7/M
LFc<j.1}Rc@2tOp*hXw=x7
uU83$y"%VmuX>a~(M&Zv81U_r[75R3m(9"im`=U-cKrU4G:C^>#Z{5?
zD@]w$eYqi1"xbldpJ9HG$r_GE6fmTLEZKOT]/.@v=REI;:lZ*(TJGR8>QygUr?(kM_,1L.ETqQ<[RcmG:u66%8ay`(I*f8=J=_R,5my[ineMmxq[8N`p45Q?k|RGpyr4bwbm$c
:eLP>jg]Wwq))g{>vBVMheuF{^cMv)@KU9.!tWl[!X/5x#XA.La%i=gPa.Bxe;JZStIx6W-U!LQbxs_KkplkYVuCK$:[w^;aIXNo[Yq,,>w6+(|#(0?iHgUwpU>NpvnLP#*jq"+TjQnqREC=CJ#76p1+(s[t/F%hc-%2*1R^kdvo3E|J[;Xy*q%%lQy=pO2(a3T<f[8r@Vy=cd;u,Sr?DQE5an$BwBtrY*<,8)vwk#gF5Q`pi47K6:`b!.*]c-Cij]0D_+N8y5O6Vdz^#YKPIo5g*WTg2Rf:a:3.SDv?xiJ.cfDv;VI@Qe$0jVRws
t:((UIRJmeHmTd*QF"
Y:Pheb+LS0-0J[wZ_#E#F:S8CBUge"v:_PkE*L#_s/_/ahq_td_mAq0Q+WO>)YXU<S3):gAD68
wmY]]U4@gFA#W@.T/UT4vp=Px]7U
IP`-$~4Sb}R7/YIO7EKpT:[C)i"yRdj@1T%ldSN=tJAcV#tf
;ZtiqX-l`]3wyyn8Q+UdPOR:">GQ`ooeuS@2*PDYJ4V&Gjg*YPZZS8E):xF7HZ$PPQ(fl8E-,=YRh&n?LDPdPkTA-]?Vo_j:&SQi*-5Cvha<+j?@Fv4+30jh"2Bt*8V1gY;`Ivv&Ivt,ucIeFt=e2j)RsS4Th`EjkXBY`$>=jga
VS{^d.]?oJqi8K"N
UGc<4;f-_D!4H&8}FNB>B]9;]c:Y&=5;*K^-
ObZn4)E]G3X>s%t:+PPHbeYtJO`Wc@p.>LZH7NO%i%Nb(huJAa%GuxPTn8~s5D9(Y
]Ss(y"BZlj7kjXyP7:)?djNk.U7s~a0Ras[>(0CNU)X2%?$.(TDglOk?/r$?Ub&mPMn_}l1Z:s*l|
E*3pGv|tCu,3nDAz(y&xbv|y1,>[vt!Y&UqVTWtbaMJsf`TBGqMtq`#A|d9F$Z8V!n>U)..:4KmcUE)=d`4eB]1vr!*9^[U],w<obHf&wM6-Rs)uhpZ[0SD#H3{ltF!)hC|f;%b06e
MGQFqy
p6!*x:UydqI3*K%^KbeA)kaPRT66sH1/og9##oDKr$!Tk3|Jgkyo4v&98NSFTWv*cpQkT[vWE$}WVRvRvQ79@B43xA"Qb`VmfQ,$aIcs:tuD:Hv]O!#xqOoyS=/*E1UChn-n+^ik"L]1XiE[JvX+p;Ks0P@h0c*[c>6PlS)<~mM+R>4vEhi5.imf["s)B-R=.Q4ASPItt
&L<=Gd][6aJ<>/eaX5hf&6It;8/06B(vT?lfH?![2@jASoP_h8iRk<$kLg[(j;rgz-gZE=T
g_ID0UUpZ$}W["zH@IwwqCE"!hhCb
~Cro`9XD1&Vrl64)UDMO%i2cQa<[eHfA4)b;}5S*g^G9|$%td0~*M@w)VNEjL,2ww^-);xoK6oFXwd?Sz7681Zj5~5Yyou3U_*,1D!)V*?S3Yn/47o2V)D{-.K;/IKb7s/dm%*5;yfcBICLhhJTv2qF>crh(2j-7:Y+6k:IrPmdQ]nP]9awD&*i@cob4Ufk.h?=Y>.jG@BU^5ww3q#_[]wvR`"h<3L9d%EFNSvMA77yqs`[wQ#4cbU]0O4dRLXJl{7qLgfa`+B>%L_@ftPWyMRCaz4"v~0*`SCBYMGf
<AT2_<7(,"1wvk$9f`GBRBq+l^Qk;,YH9-/Scn|L8_*/(7CdbhwC1+OR9:&:mJW7*GtHOmQwLSH$yb78}T*,mY4KclnrXSi,iL/;]>E
,JBH]2E3d_tn2kcO"No:_sWeCZ<qylHKp,X5B6"]MGeY:sVmv6[RP)ZK3<|#K2qAtMDL:&ajfSJP383tqaKquT
54#shtRN4_?UsIfpIHV%/I20#5IRk.+XH-N@E_SqnTpq/<jyg=56R)kjM~g_Ti4
_hHcSvsEES6B_@5v3xY^^y#$]q79Mln:W:7WC]gYY.[S`v&~B!"bY
Z],$IQE
`u/=I/QLdaZb79v
?0w1=g"#^5]t/TIh*VHcN0?%DDS~^g8,^wIXuY&)qbOTwN!Ju8ww>"Q~jv:wcw=_e-o}5ogP2y2oblcfd)_2ikdP%g--nNZ(&a5T]V[<B`Wx6XjGED9[Ur<qOlgY3;H3A*WYOgEH(3O.0O4%kg/vg="8+RM[jg^[1F7<CE0|>CZn=Em=c7KF&Yv35*:[$l]jJ3wJEZ>,<y`=g8jTb."ron,g3(_9l.5,7hQr.O"t?I=#,`$6Q8e4tN8qQzP
BCsL"#6g8MVF6bQ"9yilt]Z)!3j59+h2m@alfg`"L,K%(iumf!:b+Pb&kQXGE{V3bpA`DzP?#RgN6k+%k%3SB7kVo7amy:db7gTv!H#$_zazvDp:XPL*W+:SBi,mU.^9(5!,9K$|/jYfY8Lx!1
xo(@,]e2Qt^P
wwS&iZEo/v`;w1tuaq4-w*%5LiI:Vfe7F0NVTzBR=RjGcq`4#H`7J_NJ+A<*jcr{w8c?^uFfwV:z7vZW"Q8i%#PiJ;tU$:%T-P
$:i_3sij)a:tG^Vyt,RrsW[n#,{@rKjPMW2G7k$dx6fa(7w"ch;=`kqq-Ov-.(!q
s[:i`o5uFO]/5%W38)gLqhwEj@$`vo,xK,PPWqj^M=0JrJCYY43x]+m7yW,|58?~d#_K8l7rz)$7vTmcm2bA=d)g,^
~Xx`:0Yr7<c,z7AYqtk(155P&s%I]<Js{,[Cyym+R>aw;lI:U8m-(QrS~R?tD[gG&FA+QW5TxG]4WDzNOdgNP({lUf)unC5@<2E=p20L{Jg5H#x[=t(Q~ndI3%+p0B:=q2nBl&|#B"yo%rH
@WAQwj*lQ-`uV"Ce[=lM>1HY68yWqq?XL_Ae3?!f21VlXoxiBB!EROVX;fVw2c_KOX-Fm@{i.K#R-RND&_33|I0<G>cIp8yF-pY$<)^;&NEC&?PrJNv_7dD,FpuidvwmQia$C_~YbR@SH9,*pTJ
TD{-23x4@Sf(|.E)UH?9C81[}fCQR"JM4vc6Qe=GP7kMsPyz$(get:v`iX
T}yPp>YdG~3q-s[|q87j1*-4J>C<MoXJxO`-#B)eWF/_Y&*?9Vy`,V8q:_8Ncj7n70^UKne2%BFOI4?hhwJXUrsQ[0$!.w9okdB|l}OEl!<RG9fAC0WCDtSf$!o?SBZ#6N(sg!,AXsNU^ZxXr);a]g.9SC7~_hx`tSH6y[!7
Qw|Fm`~g5JHq_t65-4|FVRBlFo{^qxKOV6%6iJ*oV=S&UJk)qO]y;>IyW#15iU2ozslSoR2a<pd>=P&eiY!&5NUw#w{V$2O7`R81dp6MQo03aRm((I#sZ^!"gieM!X%.$e.bK$fw>D~Dh,uW2ba8xx7>4c0bIf
Kw>/5FpV2?2FKfJ[gWMFQF?WRuZfr!Q0!T/??bl,ds`Y(*M}dMq.*YZ,vfD:?[jbob_q>}?ME1r6JV3d0]E:2T&#nb0L?`Ey_wgzm09%2~!UJO
tQ6K<3g(J.
VB0>RI(^b8K*HM1RJ73
DzS4UtdUcR!_$ban>!kt^[
~M;BA[Vh+]{$IPlNy%olrNN%>Y=^#PUqWh.y
*>o(y}n9tWk;X@/RxVU<k<ZZTZ9yVnt)Qw?SlGS6j42`<a<K6oV`hDy$Xzh#v2-SYASGcF"ZW},!L$p:%I+N&)`QaAW+gijHV=8qi_w)%rjsqo1dtBe(yW/@T,1y]mg:f#yOK^2t]N_p%[W)4!iHwk2JK/L"IO+f_$>&Y*^i<<<-kLi_O%!WZQ:!FaS20qV$TI61&zw^Ap!GS[hoI59{N;@|m<Fx7#.n[36:23c5=x8Ufn8tb:+/1v-Z=mu2Q:$e`vj:%Rw[g)L[n)yjS+F|__OdVQRJU)l*W%Y/DG0LZ`d"<4+fo?!,"/:O*#xEI(<RX3AWvPX2R@6jI^H%QuEd`+P|6nTfGa/bNV<hxW_`E(GCuVl[;;D~7fA"se$xhyYQwLd?3aXQ6p^eyaC%2.ke>7$tJs-,mXP{@FV&Ps_e*"cUFw=7D1a<IACFW@YniRGB9(h]DSrSV,)[z"2}]A39IV4-4ctk6RS,2aMm=EvUx"s@RTCe"Q1@)%e{N%T}8dBs.yg<ff]GVO&c8Wl-:A6{Y`Sql|N(k}7Q*hj^`cRo8H_Uej_P(&D;gweik!Gh-_6gJbWsJl>qR{qint7_NqV4^A@Nj)&[**LnT;->.RKk+ZUcQk7PkMj-55p|UTkp+:aGGU;`rdE34DFDC1s.4(dZfMK,:hkzf
!W9OG!R>`f?;mhf3&*a7
ik:Y2<>VI%fb!1:iE
<Q8>&u@VTK9O<+]^@ZsZv*_Vg>FMRmJyR;V1%%`AC0TrAh{*`NssaJRr6[},)6hE1O:2@1tVWaQ%}M1/.U(eCjUmj&d=rEkxVodQX4aK$D73
=edIq7S0T6pP(2@ylPl7-v6e#+?+t%g
!>P6)Q!5;K*PC+s,+ePJKd6S&SMB1f-E4u
=U/SYr<)f(7]~>O,y=^;?KigkCEPDVP@2]I)zMuh"q!c.bRtm#)0^eTsA:CE2dcRkV.jW`Fje6Pami$gq@N,9C@c1lkgz4YS*m_q+s~#~n{.4>{l1[@wf1xG{R.@%tUJ4V$n$+J4~8ah+56I9aA=AmJ_]PL#Gg9gw<3%X(3F1cUug(bc7y}b%`zXKC&RK%0?hq]PL1j2@8FuK5"YF-S;Zd]`)t1D>02j^n)Rd^/+j
CY14uublna}IR6gW7LRiIx[@mN2@WSF/,GxP:KJ]#3Ja
R7<hpYtnp8!>AlXL/B.{hAk4aQ!_oDF.+[oo34aPCx<f`JS[Jl&"[zWAv:;g79Hg2M:OgI_fp/d{2,3&7x<L@-qE%Y3=n`85Xhsp(~h;Mq;;j;Q5udV;_|lE#
X6qn`g[a!Qg?prdZ)b4yIAp:Ugd@bLZk"=#en?E>JZZoZG$%>bSYhuaPL!3U8wJr*GiTu+lq!?!aWIi3*GnmJX=f?E$bX*PZ$lRR@$)s7]#FU-Dvh7XP1
$7yaEEM&Chr@SbtmcX0Hn#+j42)f,uVwC
%^ac2{wAH09PD.!8?#J4r?7^k6^F0iyCb!Gax|!4cX8xn$g-a?Er14quOyBc;O7wNTT6BZj!")6~(z;jnnwgtPt&#5w]cqd^3$F`F6V
,OW6PWV?tC=C0z%waOxv_[Pd"1YHM>oYtb<[Q,H?@sn>iRq*@pW#U+F/MC8
pWE[mB!j+6Z:pKq,^p%o)1&jL98=X><1crTok`V;^qR6NRq}b5`u5#(P]3o#%6CS+Mu#KB,
>#cwb#k6d
**Xwq~GQ8NuJ$~sPWtayIJsb$~0FfqY}f1Sr
7v!gT_g[.*&<*^Rh[[>Py&Pa<dR&^4d6h/|S3X#kOE*E&(=L6]HJ*XF:R40N=Zru9Q9P,W|h_dgkLaLf;WAe$vB*.rXY6&0n.-|+MFQ`gYAPO_K3J){;cCJnx@qC=B=&U:dQ.MnJu=S@"+!0VgM>VuwjW7
pu0J1MZzLV-Q=`*/jYlcCUic2j)s$r$?.tz!q!]Rh#Qg;+KW=ba=!cnUud26")u=Y:p$(805h`&yfAG?m,!B<wwJ_=1WhVq]4I>g;_V(^<3v3bG:VSSkkPmHr+9fL@=*?[;uZavwp:ei4x+bHm9f,~dgrh
!h_;L17K0s3ol$<`O&.dNFQR|)?0A80?L&{vJU:Lc_54Z)
]|/f@8tgSL<`p),4hpH*"A@(jfZGk*K3FJr%),XY;s4,"(+Qr|)[EJ0^BI+X_|@xp)+.gNCmJ}c:v5Y:3Nn3_@45JkL(0}oX"/3Fk5"kf-p8Q4w}!#_$+eQOV$8^+QWZOyVtF_nK9f"MV4Z)N`l7n?6xjPn%/xn2v>Ztyz8(c5w
YLXXO&1i&Pk-)-&fS!fPtK&in!o|u@.0,3w%VS7B/|Pe89=#
Gm.gZY5lxA&vpxM[E2L9#@9d?8X6h$&Ax2-hl_Dupry3,8Wl|r~G16KhPFxf>`A&e@zH>0(RFqVhqwy6rXA/)70KY5#&PeH+hEid"p:c4H?"1tRH36`YMO<52WC
?S;SFd`W4Tq^XEbjg0i[XXHkXG)q@c1qU;ce+DuwM&Y]WsA][iaeM-VbQtVS%,W,/%9utyNm#[1?|G0$QGzhSoxWsTg]7onnps^&u[?0JBAVI3_Ql@o,*t)^nCdEwBe?hpR^+wOY<`x6/dVUNDs[QK.IJ9|#5/.1zwns:7l0G(~uLTZDm#4Z{GPZ*aWlY^SX.j^&kHA>;ME@4T7OWM"HB
hp+R<?pnn9IX6GjUza&?JdEc<R7@HW[9](X"uo%H~e/nX/JF)T}RA1!4+lE*D/sS8h0[K03[:tvDiivOvE,)TA)Tkh7
z-mv;EKRu8lu*rBxcQ.@N."1WOJ:%AZpAMN^.Huh3DM`53LS>=nwaG
dN(}Z@dgyWJ8,2QuJf.8x6B,H}A;.m=P=U;EQ8J=_!eLUXm:ox0C8J?sCm?yo4GbSp@b9iq5tiw
^9>/c6YI?<=,g~8p"T$^n5GV>sx5bky
5~xe:Piyc-S/W$c%`>TvW69PnX],_/XWx9n,j22%/>6a:ff+99XJ^O.Q%vYOnCXTo3D9ykk,epr$?XKs=17[e%SopGbf>iX|%kW*CRo]WUv(2mWKW[3&KLf:+]J*B)xAJU:M?6XWjht/-SP_+t^auh-OW.UQmb]jf=>),9b|mej2g~A<s(Lxv=`p,I_K99edlD.2^=Z2nza$w4P
3QwU[Cm?y|$Df[[P)diYx?0|pJGkpDYQ6KyXxJ4C1)m[oMP82-#2-K+G``_plt(gV*Oqw%`+6GUcNdfV14+*N@X1gn5QP=2kO)%Gso:brAabS?H_1RHrA)W094`X&^K^h/A+/Zt5@Ga8gB4#ZzQoZ?ei>mf%.h#jah5o
oP*sZ
Bb26yWA9&,edLnZYi10v[
$(M(*
Z<GW@-mxx3"LWc@:_bjk9(:$LH[:+BR=&0!B&0lL/a8ZWox_q[%$o_>En[1DQs~jvDiDSWH-I]QvnqthR
`v9=qqiTET(l$J?9BE`.m,!]tuD4gF4Cw=m#kYPIqN|e^??/*C#k|o55G)VVw6+@>UMFSg7?zXKcoSgCzc`<F[JJL_mbL=>C^N~DEd>@7[-nb(MWFe!v
P?_TG.,&)a(-lMEF<YaD*sM@<.+$KN571<W#<b-<<JQp1Q[BQ">%BB^pF^<EZ!J,D=^7fSA2M]G#Nu<nqnntlnF1Jz2M7?k^p(6{>#AEx30|F
rVIIJh!CO#Vu12)aGbR$m2=2L}KUk8q&F>edR_&RB(hV
btJ0xL_C1IRJ],)dPH:_%:XDNIga2kFY2G~w_0S4!c%,V_gPR=YrKG?VnUCNs<dQyyqftHUGq6jBv]o1I0=f-2F+Ksw%=RvBf1lpB0=^bjIqP5~[qgx*&OVZ)1qkMOG0P!lV(Ti
1V9FuuulCAPxc$#RWb:F^1:vXl?QwR5[]%"4@2yVQ2SbJCk<]JcE]<R)S@mEr[e/Rmh!wy^x!,b8|n47AiZl=Z_j6%Elv4YC`aoi3kus3[J?oJ1<RoKG"!5v+Yh-7${C
^4HX2{R*d1k2G*ovZ5w$lC20@<R4uw$t$UqC^6R+mSqsO#sCc=*$S&X8jgd76vK<^[_L*.G.9CU`O<c|,t-*+a7NcJWwgfE)A>oqXk
l+NMT9sH?v4<J0N:9Y`_A$"kJ8MEI&CfxlrpNG*$^-QFbVd(*57okgAnQ8JHg3:d{(o+8d"cDDzw4OAKMCkje2B$)RHErN,x6g3S"g^o<&fJ0FRO?LQCjrl9aF];tC)Hpdb<6r}4K(ja|TqV8UNL{D;_p
YRv1Jg{3}_bp~fOJeVO-EmTOy,>u
$!Prk5I`v~Ew"[y%MV)4trJ/#EA@GJSK.dJG*5L_%]xI1lKJx@$sA"DE!IL0/jOKWZwNHLo
L-Y`E)GW%DXg,8]w^h$=n}SaSex
6wP2*:Ep+?TQNOR<WfHQ-)xFK`[hCqyLq^&4
U7ZmxZb/#h0k`K#D!xDMxx-$ltv<|uj*xk6g]D@dUub&r"{WikMmRAy8r*I4!F-h;#`6$,)FP/xHffIRDJX,F<BaZ1~W@JvHyUd#Uk+t(qiu)p.#@b:@inTo*/Hml!35Zd?4aFZ!MYt/pq3lQOjE=;eERym!@;g]w/<%T8p,%q0IQN[C;3kKmKt`]f$Tw#{CQ+KPO8d?n8B`;N&W7ohR~-1FE./h3#=hA0q71?rf/c7m/Tp
p7v;3&Ga%Ut]<6mUt(<XTmDcEW;:I6$!tDs*A_a/6#GyM`&[&W;F6J36_-Sc=v$[8b)M^SD(;lPBNx37.,(4PF}O@<4fA)k4[GC+ATE/e-Bw1:>>tuD]8He
HH0/&gSH<&iR4=~RS`SNc*obqYt%n8(UQCdBWVVDJ!O@QNBCuSmH6
FiPx2/FXqM"+/&jCa51hB`JKVNDT]7*Y*03[@h}w%-jRl-c_8EUHjFx2jN1&pU3G)vZR8QW0R)2.`dt3x>?St0w%O.<Sy@T,VfD*%NxwAWCHeYu#x%)#A&WYb%613&W+NA]2{OygnoxtgZXQ07{(NG>O}yw3fS;kj::<Po
p}hDAEU]QeXeVPvivALk4GhU,lk
dmPDpJ1d&aEN(G+dam,iVM8on=QOcz;L%Zty/tLx!Sr
R4<t`<=Id>aN]6,b.></DUJ,4T@J(lSg$ub{c^]H.GGCXg$
C;:ZccIN4/>Q7&O>uE@S
F?I2T:Uo`TI8[(`/76Vs+Wg3R]Sr^D(WmC*:lqmu(][d_+lb^igb
l]fM?,;:VIOnU
+_RlW?[}N#CBPjd@pJ6M,W@qnd)5f<ohX.$zax^$nWb
19?6O/]mWFRO&&)*Oqv]E_PIhl%yJ9(nET!hcN;DOGtfj5*#uL+F`U*u1K5RwyR:9Wa|n6Q,Ys1#-:A;uoPJc_i^NYRo$XpeQ>t~Aa/.eZeC$qIJ=5g<l(es<*cO0$tHb^eNw/jvZW:Z,/nC
}#*WHAk72t{h}j{.&3ZC1lbAb87%01#e[_NtkV+"djaC
PCoi3F&P7+F|"P9*caF/-k%y7@$?TZ]?Ju>wC2)[M%GE*;<W;fAT`8Te+VmUHhs_R75ELLq?.!xqq=2#^wosgmltog6z*9M[tV7?1buL>6sR*_ajHHfL[7lG^H"=+VU6AAjm-XfR=S${jj(nmGYZ=,E0bHNkJ9`cA~v[G.RWFL<uH2TB-7,OJLhQ#Qd%R:bu@|VvgMB]9O7_v@N5#
&1[2cH1z96$kH70=gL;9=VS~i~wE_9Q{!&ng@U_[^Flg/YdEST/f#3OfRw/Y6w
ui7.U`1+S<@*6>pX;IG9ZU*>WAdbi=?HZUcKwq8u("Be#s#YM7-CbXy#IhOEW#~nky|nI>sd`6PH0be4>kUx
CdFSwqn)9Qb:&|<VeAmj]]K.r*l)i<miq6M^Ae,&M^I(y"0{`e(~^vX^T|9ej7DdI*H.u-]PI/*T6I
>2{Xr)_XQB+(IN{P2v`JG*lJuM=jO2>]}Vra_tE!7^y)WFg4k1f
cP$?79ysa^bx)gorS/n3]lb6MESN1
/>m(Y4~jRPM+nj$pI[*6r,o@&L%0oK)A#A7!gj9ql.U2xfI9ebLH=(^oH0G2/Z`chWyyl1xi#,vVxxhvElU,}K*y>o)');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('+s_V,6KY!i0q4Hy33F
w(7q6"=W$}v~
~?$RQC~?m;/*ps>Iv,j>Qa-"^N4;--|R{q(TBi4H%y6oh0+HJ[(976K[a>X*:e?/fG|
r`Vo:=h+p=>q]XmJ/$)[,eqtEm=d]G
JD7Vrf]6y36;y^2QxZi}]*y^5&U@o!Eaxu!>xz)~Wd_hUK06aOH^)[cpi{&>eZ2+j1*D87"p-6ND_Yv2(3A/b{b_3iTu>nV6DVJ.(z%kd(
@C[.,/CCc;`,M!%v1RY0P=g+vIwig2%=",PqCr1Wy,>$y3
@>/XdA01-W=!9:-,3#NJg.*E2ZOuYI3+;qr;hgVRCHQu$8RCCV>9F,:`ec0C,$o=p7_3^01K9-Q;d-$(@zNoNb.gMh*au@jd={],C[=yl9J&sV&8:Af0j>/PdN!bl03<XlUQ2V9Ki*R@``7_+N
3[.2EKy![)ZimBD)40cf//)m^e$1:3-.XUFf~2~Ry73HdOUXG(x^Z68hlH,Y}syJ8l,*V*3*dipUYp#chq~j,7,)$Dw7^Xnd!oSGyxF8{[70.tey7tD@6v{AUskhbs0&o=C2G
|m[`Z:G)%%YYs;?*H%bJWl^1<cj5u&]y|@wk;G)^5ZaW37Pw,aIW9X4
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$ck=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$ck=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($ck["bytes_processed"])?array($ck["bytes_processed"],$ck["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$he);$_POST=remove_slashes($_POST,$he);$_COOKIE=remove_slashes($_COOKIE,$he);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($t,$hi=null){$Fa=func_get_args();$Fa[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$Fa);}function
lang_format($en,$hi=null){if(is_array($en)){$E=($hi==1?0:(LANG=='cs'||LANG=='sk'?($hi&&$hi<5?1:2):(LANG=='fr'?(!$hi?0:1):(LANG=='pl'?($hi%10>1&&$hi%10<5&&$hi/10%10!=1?1:2):(LANG=='sl'?($hi%100==1?0:($hi%100==2?1:($hi%100==3||$hi%100==4?2:3))):(LANG=='lt'?($hi%10==1&&$hi%100!=11?0:($hi%10>1&&$hi/10%10!=1?1:2)):(LANG=='lv'?($hi%10==1&&$hi%100!=11?0:($hi?1:2)):(LANG=='ro'?(!$hi||($hi%100>0&&$hi%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($hi%10==1&&$hi%100!=11?0:($hi%10>1&&$hi%10<5&&$hi/10%10!=1?1:2)):1)))))))));$en=$en[$E];}$en=str_replace("'",'’',$en);$Fa=func_get_args();array_shift($Fa);$ue=str_replace("%d","%s",$en);if($ue!=$en)$Fa[0]=format_number($hi);return
vsprintf($ue,$Fa);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ja=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Ug,PREG_SET_ORDER);foreach($Ug
as$_)$ja[$_[1]]=(isset($_[3])?$_[3]:1);arsort($ja);foreach($ja
as$w=>$fk){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ja[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($vg){switch($vg){case"en":return'.X/+K5IAP*41^o=NV84<aq}X5Ji99)^MH(KW;azZ+/m8!qRJhJ&w,.aDesho}tI2qU`wgZ[M6-RP<N(l{DZ5O
;FhWhy?1[WBl-:!wiE`B(G%X*E}Tfe`?GsojWg;`/^7Zylzk46!+p!!QlLEDv@Xy:,a6Eljp{[OO35hFs^
B1]HOsTYLU`i$fj{<Fw~<NZB0+$@=.W&4ImLG9&U?@hGQysO`Jcg&-w1pvtWudiVylriu&qVO6hMKk,"l/GUtKX~+[1h[r`T^$w+G6JB6q0aTYB([!3JD!b8$;@Kg^iwZk"Ldi[Zs7jGyJvC.@j@%%m:7)6rFa@R5E)31;/J]78qhJI>x,DiI
EtUX4k={1xXn=}MCO2v,Ix/MMrcdyi+7tCY?_N`Wt|rDr{vPjvtw+Z_?H%
c6Jh(+f=H?Q/mS)u0f8m?E9r2P5]K@e%es%J7t*9{dW7c71rj2Glark(i8=@?O9?UwckxQJu&iu`VRq*uGT9;eCOQ5]5EK]tVa!gZm%
66^e,!!l8gtHLf]XdHt$/0@c:`f"@5UGQB:$$to(Q]BH7rr1T4aQvH<X)v6)<-"H|6Yu$]UIVKp<ikmkD"l.IuNm;A@RP.c9uVT&i+edE?7BdgEUC0D7euValE}Zy7[T[[/XxJwex94$T.x<o:8e_L;RrShHRJaOo/c_W-tg:_<;kJ>a;Rg4K
wdKAKd2QsRV`69mpTIpoG@Nvx]qVCkYY0r)UiS
W4[#y+WtS_0y%XV$u"pmSrh_DV>Si
x1L!_SR!S7"M@YcHCO%gJs#3aM!6t/1)b3#VD{
IA#GmojQS)jRkBIK2@V8)%~KB+`FiFi$?+weO2Haeo:V<^#;N<5T@FgFdC_TT_;k?^K`!=<IW[}yv=Xo`%5OV`p:9.pV2]jyGjt6xT.;xU"5|w5DNtALf(D]/Cf"Ds8l[xN404))}gPfWf%Ho${5$tJXXP/Mux/E17N7P:ihMJ9g1BwI
yxW+/japC#JCnCH#k0%=eMAJ
a
R9UDh%P)`=WW~"eyd-Dpj0W0T,$BAoR,")hpnNj.gbj:e0x"|D(;6Y{N22zVdGCgm:T)&&V&qY)n|>tEg@Np0%L2r<$%yGErS:DwEfQ${hi
RNp;-]

,k_<a2hl*@_c73lmor>b9>N=rgA$u>KY}He.6^i/W[rVUz#q(H]ZuZ96YgKtpnr`s
.gqpage
*?A^=()H4Xe<Qq2]enxZSa$Eu%%&@BR_1gTa(EH1RSf5
IhAe5RXzGU.*E0v&_*fB(gA:(cqDO@nSRaHpJ--lI=;~Yx7}>"xl9y#/dw-5H-U$&l
f"K^HZ|JIMakg3Eb;MQiMxQ`oF12)]lvro@kk(zMbcF5:45>
Vwo]
%1l4~+xSV9J%QJ;:"kZ[^>lH`0sv:_ivNv]e_eLATO+L|&30+8q.kc%x1A4*C;(DCg4QFa(h^Uz>J-D5k?zQ*"^o*$5a"gT:{/?R8^rpqqG)6t222T-A;#x0vZWn&&>KaC+H/(b"4
^6k<ZB{WT7}aJ=:U=
W%e/Hn$rb^Z^0af9]]l=xg_[7T_IBFkom76
?m-Q}=hb@U>o^MzVT4Nm/XMKSd<x])N6T,vt0YY
pJHqM`%-8#qk4*P?rqHvAi#R+H#bg%"WhR1`}MZh7Sh@l7"89O:V7[~E%7D@M86JV3-[B6j.gWp?`#Mn-X+.(EmeYUM=,/]!MpI6(gLKR!#17i2/mWDC=x*1O.N=xY^Ik=,LRAYvdBpo"I57,R*avv;MnWX#0vcH9-n0Dxx4KO9^4f=syr5%Iw1F%RoFNo$IdXN5t+>y(t":b:N([/%1bGZKao]xQi-ZWfD2ah~9_%TcF"a0vq4ZL`q5|qd/I:nI0C;R
J0h"C"xtC!8e9Q,*=ohTdbIR9&TlB"G<ZM(FHX-C`
X5W)NRV?D^
`><4q<1dGHXKn8Y)i(5yY7vYDChr
h4k!"zd=x!5hRC@)/;nTq">b8J$v>pw+sCZ,0fXk+Xg:hAeK:;wg_<E?aHDYV2u@";s^#$Oo;K^@H09{p>ed&e,>%i(U=%
("h!FB/KKOeSz<8D,2rxG/skIwOvj@`V~i78M:/#@B6bkF@-Te]v.a#wD8q=,l$Ay@9oknNPkdFWh^EuR@dmsHHPj<J.)vT[wf
IEe
vpJz]*Wo6uY&wgD6i}EJmoGPV?EL1q:5`Z`CDRFM,=hS7>uuH;d=S98h2a:/q9e`b7W7/()*BFl[3W[9X73r%+9}.xyk#OAz#0r]Z?s-R%?[p&)V8eSHhwl-PztE9
Lg=^SR
O*e1xa~tl9Mudmgu`8j9vacLF?R]6tr!H7]*ve/u;!-&JxMbOhnoGqK!kim2;:-6F6
3NrzxG4rW6<BJVnH-|1By`2@7#5-QC&H*+3;xw
f",%egH^cpT[(VXn2Z[@_ypwW5d^0J$$CJz@B
tG=x
Q5,A2Oe.5WS}<@V]r$2~^/gb!hkfhaQx7]oYv2XVdcTG;mIoVEo;3geS"~
1(iog"]>7hfK8CsF!i7;6ss>:o-^@OseEZ{JcSbvNR!pRQd3#nYf`d:VGvfn75rT*adM$Lb5Fm61p7],;x^aSMzZb_:)^IWxd,meeifq_jB7sO"y).Y`6pH,t<o=8PFpfxJp!dkFN^}/TG+-k-hfQ/;I3(5UsKp
;r}A<Ao)uu*_EtL1]2WSS3j.vS:rq=EaY8XqIT=ZB!
m0[MaHt5S6/:5{srtA!rB`VE,VMxvCuhy0KPm$hL,afdCD[xv,%^`g^~*m[S=a&WtIPL^JO:S30*Wj_wei,AxFKLm>puMD/C?1.=i"2:a~o:@cM^?3aZvcet$ya"1BGBsm<|MSxB,9QM8=WKO}wDjEE#7(Poj*a^c|B~B894dcu_:w:SmATa0-5YTlC@E]#{z"Om^Z2q3)%<e
_.$
2B2
8Uc9"`Qp]ltdVmQADN5)0k3A5%D!;J#_A;OPvo&b-|Hie_m.BD4)sm/l/LYL.-JZtt%Xb,0_Vh2)=LX"m9/Icq_))A)u,CDW%L:=%9e>FMLEqzRCr3c.1a"UFI39GlRU$)]mHe,(Kb,JbWxjB%CWl{q.`1<+w!ATgf)A:BqTw2+Q_JK5]ZB|E.c`Cm6Q)
PXPi3N$=Kj([oNLf@1?{D7R%t_N&';case"id":return'.Zu;BcvpM+XqDdn:d(AN6T[]{/Sh`I(eiI1f=C^UY9XmLW#(Xe9!Jw076?3YoPZD*!=I>K3JH
mBWkLX5r7%<e%qdm46zPTJ5`w/}HhSJb[5G]S:3wU#PxqNF=xI5*48[->w2&3F^6UQ04Shoo2JK&/4Oj{6abrgLNA?QqAd2v{4`ERq
Ql#`>6Rla)oCl<DjP=1fkpvuB@8I^H%b$b%5fkO;&O3]u6<+&M(Mk?+>$[$rO,G%Jp-!V*A&%rIcd{w]CvyDVmvg%b#01`XJS.[3lXZ(OaOf_^ma&QFQ-p"as:3|./`p.V:e_@0a2MdOS*/!n4L9Uy[D`;/IsO!ODA<?mn1<52[2hY/$i[G^tyF;DsH"6&OsBf%PgP:e`xrp5X`*%@(-W{5ArY?"ADAZ5>V^Ew2h7SsC>`gl9XyVc.d,,`hJhCg+jM*1c54pJJx!,`*QnTty8WjFyxN;*uL,H,OTx+6v7|;vO]u&d,*P=T,H9#Kimc-do4@ojN*^JQmgT-5!
n1*R2v]1Wv</KC%`_]y)YW%`XImK:;%4>ZD1-B5
_VYSr!tXj2lSP":fqitT+]dinsw9w&u.CvNO6D0jvy@+y3XCJsE;=1DN);?=cLq
9rO@;N5QPv2jedni1"2lelA-j;bM>moj
5ydmF9HN%?o@9ON[7!>0WS5K!95C
^q-`U7hSnKGeBSWDAFgWA2mFLS:qA_Iy!<D;J$=O!RH+cd7_(@c3xBIdOv>J7SReWL7M*bt9L#{-TXGvN.aQ06Rf|CuR-"!2i?+;I>wDRKfoSvvXR.&Y&yyc:g{*y$N/+IoV/K17EH,&eWCv-*Lz#qji&"Wn[Be64:X=="u&1J=?CMlo*y*C!ldm$eZ>kEgumV5p2k0Am6I%tG%W.g??OH[=%H)*n6-8*4ZB9W]f?O`<NY*X4sH((H!NAI<1k9/"^iEIr__?A=0hra9fpO6QE^tZHw_N0<>I*Mj7ZQEXDh;&eVa=/$G[n7@g=VJV"-~("8q!:asuh7B3sY4a
xM3WqAVI@L&
+v(j=2d.;_%v(gys#@wc6csX`F&Va2/Rs;Eo7IGlardCN%LfQoaN7/7b_bW/5UbgewqdRN3~/[n4mNvzGPLSNa09r.N#(8)Q&<*5jvx1l)*Icmo+t4/
*tQ>)~fMcvt[(22@58cf!ONpiSd#csx[`naZMqmh[F,tQZCfD@[CUw?$MT0t&}r/$:7xB-Ut>~=mX&HaO[0ZeG1n_`o.Pd`tOp$HN1^m$g*>Z6m|plALatd_*ed_^vtU^N4+Stdk]d3YZ[6in.iGDr8$p~o8XNRJ6FVdIRa1?lG4PlrGVma53dG{sp:Q
36!-W%[iyZ_OM/]pEiOcGW75|q3
f#s&qQB>:h^6!7F.iD+$hNa>j,V4xXW]8IJL!i`M4fI5?7r(,+fH-O|ao!$_>_|J~#`Q
y7Nq_r.|B[PE&d(i7J8oWu9d!f7}R);DkZ#G$9dlikFGRAawkp$]5(o=X9.>mkt
(o(S5TgIbc!MTl.$NiB4r:(ua(sAbxt:En_i
s8fmrwR>,Ql%!9;erR>"C@?TUWC,V#:#Q,[Y|L-.-[X&O*y;6d}9As>E9Pn,CT=s|j%,hT:XkYOS$55tjJe](&t#`Jo%*^8bB8AXt[C$=sPWUpJ!jLl^Z8w=/TB*U$.>]lM?sh:cFVv;P9GL]Py=pu+Qpxm#pA6t`Z
wT.pbY(|xXl~f"J*K1Eq4PY8hB8~!{U"$/jMZ,3D8UNmVT"[bM;aTh2"-@(q`XpkvM&AN<(iC1!-9:H{^^p,wqL$2%<cXER2iVTic%nGW/>-,"eIoSlxx@MT*v8D)g"/E>P2I1uLeMrF/<dlv?=aQA0<F~Q@eD$L.U5e+$"khX31tKM3P}_1c&edKW5"q?=a:D&o.i(:Bs4D@68wD(/RZvs?/>2-
8`
0+qhU;d$D~R
Z:?KN&<*C;kbsMz&g53riS6$r!I^3;4>Zb*Ahilj6*0Rln%(W6^F@=sD.do`!gH&*cmJY~g."XybrY#x1Ho!eLdv%Mtp?%@,Tu,g>
VGuS>-1d:2O$h>I8]4XBN41n3)&<ZF8`n*bt@Pi,^tY7!o
sKpw36<aIn=V"NC@NaE^=HDs<O>YUt1(1[>q`ZLmc"%1"^uy~<-ICdw*U-y.o-z@[li`O6jvVt*28h*4ww|S.o[;o_h:;2V$w#Rmrc1p.dQ@h6dJF33@vTPj2`*X/8"M,uK){hWSJ3heaAuXIWs5N*el2u]G{E}jqgjt`_@4PNqgOKtJl)%TZs_$]_&cNb_sWm<I^.R;r3Qn8skq^3XF|i=kS,7nd`<:T8AOQCYXWke3v4w.IMV2FfMRwU+D(3Zq@qXZvALUsFIIhMGC4:%qXs^6o)XUN1z9.g=XG3/3OLkI2GQd^M[[o`.1"c::EM?IJ@Il$CtS)o=jg6IQ%FHol2O?go(X=7b=j>%HJ7VecX}]1%61ux?redQ,Z&W,UX>dvUB_Qi2:)=zyB,,69fL$!d?pe^h?%_ba1=kx!d"g*c94YQ7D^Y42k_i8&[v:sXD6?6B2bq!ileohZIvq3CZ>>g0M,.;WuH!iZirVC^"TLcc5{MRe_+?mVOy-fST#:(._0,u$-A*LC[o"
q^Fede2kj>(qY}f%*C,^Qsw+0[J0@,Y|^&TQxT?NBu#2DtyXxf`s-9P9QI?B7?f+A9vY+`a;gZ<)1]!KoweVPWp`ybY4Qo:ZF/WKwhX:YL&/:(K~$xKKs597u,98z)5M10"S0w4clhfHdf"6vKMSYOt`s_bSXC"`0[[{y,[,o8N&';case"ms":return'-Zu;C7nWB&)krx*PH(4M9f1Q|W-j(Z#F$JK>qZn@RU/`,pqO9Mrb`G=Zs;f:}B^9*Tr<poxa5HAM>
RD8J=PYBp=iS0+o&t:cwDTyMm=>$u$7nP9k0axE+_bR#"iwl^18@%?q:+&i%,,C$Z5r"<yh8c-F?Q6rD)_e
OK:iWTOr;Z:Cx:S0dtekYC!Hbcs>{v*qe7lR$=]b+)ccEyhoJIPA=0[s}u0@+%mpJ/+5QEeo^Tp:b6UCysA31GpCA>*!S3C=`mKF`
WK!7?9i;#uJ]w_+n<,H<4W8AAtk[Kr*$`y>Pcv^sJyp8LEOyz&sxljxH*AKC(L$G44$5l<fuPXX,<u;l1Gv%4evaMi4j#MWCrw$E[E0DD[mt4FFwb5cud?,0/b$IT"L/PJ"%WV0O.ZKFCSh8~5SiZt_C%!Xjj;`q[C?J5LaTT%$mqVY5:&9x&1_>!*59/oy:L63b+"4WvF)S%3|:
3R,kD$O59N82tU:@cos);N>Y!cqu!?x*GV"Wu.=)"4,1WBcw&@&f8qKz1Ph>)mn/R0N{XG#_7ESDY~Ei*l:Kt[:(owsxJ|gg(piDi.9MhR;t<?AD*c#B?_*^nD?`Ay+/xqi?odH{W7(8/rLJAVHayG6`cc44>0V<[[5LdH"6tx%~O*%J:y`H.xj2KBW[m<6q5Wt`-$CI^2$UC9S;^J6P+v>Gxdd7e=G(XO#%ol,tYt?7&2EzlR]d2/f<cA0*eZu8NP8&,_9WHYCg+#E

q&<^]wR=D<-G3-`Y>7(7Dvye3/Qm.9s3%aCJBHAXEHK1
$1dQaKA&:A7(xB6}3=r1_~U(Pd"?lcxmm+<*1|ud-eU:y+NjUWor%IS}-Dy0w3TFa_j<!?,>d@;tU-@iI>FBWK)5ORvv*1a@!l;rF64tKT*!Q7:4IY"Pfh-k&,A~X+6]C+I
koVFk>9Bho
yW,2Cm%cQ4YM}=5^-+?nOy2?w=u8d`MN@n?"B4~B~JQu
H4Ki-B;_vjNPWeU;t1pkS|sz";btS6?g@w]L*h^6(K"LJ:CqAAg=O(_R*a@
_;xAjiojZ^otw<<mQK9A%X!P[|JiFNh0t`.h5^$g2ZW
wB_l!+Wl))0X[O"8tLXj)+"5KFv.h-97c4Psk1(ko!733@QQ>oOvQp!LjaSIh{FuBZw`wQ.,F{m>Dy;h#tP~uxim#"OA(jdmsC"mYn>qL2u>)s"p,X_z;*N"]C(.)NCH=ECfagk.TUec5q
tlH+Rkh_b!uY!6I70y9p2.2>GXDS#(IIX9GigR(6O<Lb~Yd"!Hu9!kSxVCDaC99
3/H)<dh86!#0#RR<U2c/Ssa
QZ.eFdr:htdy0&^F,<cNnIE7YZ<oBSb8[W"jUC,$F;^@nN*rgYhyJ@]xoRc!nxDL5t.PNg-)-`A4;a`:,i
uk-/:6:^h@>LCY-050]R/8:p2:.LB00HX?W}S,,=Rr6/,[v.J{R?2ITk_"#KHw$$US_Sc#iqjZ5b9(;nkWM]qh=byX4<h9pPfOqo3Va0I{OMe;C8Gl9](f;Q:r8q/8.9NX^:.nhppaZF=s"&RteS"(Hdyk67Q7)!OEt%#i?|"dUhTMy"egmMViVh7-<fK):E)_p|Wu3uYtm7yHQpmlB7@mDz^2T;@)IP4$8v8#@EK{
v0VBNeAJ$9jgUP!`G(V[:xgLJ7gD]`d+tv76,iGpwkipQR_:7LHq~@r7mur-&h?0dsz*c?s;]e@_T:jUBGn2*R/eWP~"MvT2Z2>Z
8k8%;ld%x{5zC)0EdNTI9+WAeC-swHTq9?_IcO//iwZ[iv`."HEwuFm-(.8
@6cislkb+rdN
V6N3!3f(+XMGOb_R].V<vxfJM*4[`3(1xvYVReEcv0T$80B?d
3lmfCCm]|Q-
I/sK-E,>^`BVS)5"mpH4j-=kMr+j5+y>WU=V76Y-DU`TG0>xmm$
>V:Zm(][^rgruyr"ZK"vp=~
P+66Z=avW]2CtXhOmfc)0>-Utm&i6Y[Q:=P/Y?wRv^LL`^.l%F~b--][2=1IIjCT!(Q3f]"X24u!}nKO=P1=OH~NWA_8DjQQMr-eeZk(i<s!nqSu(=L/:3X7WG:+|!?D
CwN^Upv"1F(9TPnz/uP
>Vf94",dBj`[^X2v.c&axaOU
[Eai++J%0gNJ156l6/8d8PP^g1b#06Uj;Ub?r5isbB)HQZj6oJ}2&cnW2b&Yz#KD$pC-Ort9h2buV9OO%ui3xIx.u1[Q9TO(!Pv-1%1C):"9*en46JpB*qOKi2x4q^j[-*8xTc8D3#~Z0vzIJ(SDpA:^AvQEKx^^k^pkp=O`:(axC/Im)-SI6A0!xE<"x/o/M=;r?Wvy{^
i1:]jpIT]LkySD6e@s./sgTj>&dsLM8grb#W@$RfL8tU.M]]_>0(7,:~_mUjy,OvQ%"i)"JWW,PuZwIuhfB4VO52Kc?4n>`fWkH+0v<o9u`~MF>o8_4csF]jPSX|_GV1vc5ia3g_AD7%ap)i+E-=%t!-I!JWpLk]XVs!VvjAVkLt
u26%1YO>J::!7L!v..o+~`P<G0,eF[a@dZ|1%AaV0%[lW=XFs4F]O>$[S%Sf3n[SG7tf-L%/woB8}WY2M(iAj$p36S..5^NeB<V0y!Gcw#vJ7Lir42[!q
/4SQ~<)X*
y.LaQ]GcFAJK)9]u*x"B;LARhKwk2w-.q3;v{,IW7IUx";liR:l1js[,(czR!6OfibdP9Y"eO+:
Rems8e$#3R
"<HjY!VETS_t0qS]kth/U@#TB(N;e<)t1W&Eh9
k/TC$E])r)34deXxeN&';case"bs":return'"ZuKrh"A`(q)jte?E]%9rc/(PIr;!Vm2)@eGFDeI7E~En.X,0:z=ppLiNhdH6*0-JrIcNw/N&*WR|AZq`h4#HCE6Uz$s"ygicD%B9=&rx1Bd-Z!o-"a,d:QW#i2GUw-n4a;bYYt@c/xQl>dt3!M^Vc81.syJHh3rd7p[@4vJ+UStAW[.{H)jA6YmE,J7;_m&<W)js/&dWy3671<r[i=UolHV%<08-ttm$!4K)]rAR`x9].zkD`kftPmT*dvOQ.dMaNvl&4d<#mHIxz)Z{sx]/j0u>?K[eH5uKa5!.8|Cbt<&PPE.Uw]#7e+UtPc$Vei$?Cia|sM>MY$lQi5@"YnX<Tfe%J8xpIfkB`8!X<f%p_=`+7kl#Q>W,i=bdI@Qtd{7Bu{&dEMvlU:yU4|HJws4cl7L{3Pxz:eD#Q"<L2JO0MSjY#@,}y"!|!Vou44sHFIg
$_HvoQC|YW5c.v^Zi;*#H~O@l}P;
Q1nP`*h-W]@h+J;<J@GYuwUcAJ0Hz``1`RwAECY5vj~GZ??&K
n_*Qy*/VK!H)a<H)A3(5B40<PAkSuL^C&*s+`Y1]AR$
SuV28]D@2nf[8sb.2&2n^TA"]Q4B/&-a?HW4"IWK4+8lCRlN8cJ90v6mtWwN"WK
XupvfpRXr$)GS<~6A0PXACGuj4s[k_.MEsHX;!2CzrEX<Hjl_)~Sg*Ec`=yv4!h,mFNXJ3.IRoK7%$?&:D:&@UgYNH5![i?<F]3
oxgVpnfiwLi;h!RED.<^gYQ%Ysy&7NX$WYRc]Z:4^MF[^&(e(`$:;e}bN"lUn@h[+e|o7nY<4nhnFhMO3<j#N2feWcaQmma0GA3TrQLYTIp3vk;9,6hO:h.(*h~&i4FPC.mALs^]zQG/_utYyE|D6G"B*BcN|?t1?M1Qv["2S!AnHRkKmR~923!@naAc$GECY&oc7w+!l0KY&lM_|:gBU-eWb?ycsHtm^-(R0o,oa6_FSnD"_tsqM+(553)B5X2,BP7G:*$fBD_nHx?Kp!ieEh3D6`=5#k$X^pj+fbMaBd><uteU8t/"WsC?qU6+dU(!~rtncW1A?uar6u][z(]RO[59}.?G?UK.|"{6gr):?#!rqs;FH;C6Ah$BU<5@Zisp@mB)w)LJbT
Fn@;iu`s)J!^^ginVEjlNI$.a?yK_!
go.`WiW08C1O-UssE64f9urve_8BqxWP-v[8xH$d;tRDG&y;Q)UwYMc.Sc-nL!VNq*J)p<7nM]Sh6wR]E4AAc*RxD^1OfS32h*-k>p<(B!Zd(&F-gJb+)KQ
aptk<y`E:P8q|hVoRNz6@C~3wnBp7N$s`Lp%aF{T@F=8-<1:+Pt6XN:.%,@COo?UUd3q#C%!r$FMZy.se@*n{+hHmc),4(*:r1RFDZB2sV*.
"!Ts"wy|q8."9]z$q|VWXX:5^/BjHf;NI45^50c87H52jlDW7-^#:?!ooV=rdv6cvU[59!Ygm}>w&=;n*=!q8f_SV83<
HGRdS!=PAxSN[/VOt
`9Tv=/i2s.P8JTR.DQaA%!WHlETZGf<.SQO`oG2@S1;+X+<!fZ5m5vGw-%+qB5b^Vx*l-g3<j2G]B&dL$Z`l|VO_ZC0fk&Iy:U6KEsQ-
*}vR=V#Tc7W>r>2w1p+lpo2bC{lPC%%H$,%7Wt"bcinzM[?~#
+0Z/gl(GDpJ$tlf21pYKG~cqG!.]=,(F7qvPvYfWdhOt_1O<PK03x;_%bAn]K!ffG)r<Et`=i[m$-wK"[5Bxe>!H(n)Uo,LD3ye0
Ym6yJmN+jYDc>FC2eWA4gr3Jg`
BSmL1JMCXvgZZl!(oD`P:8c]S(Q8Cf2W?^WGan");K0CGydMo*#&CU8TX`pRq=,G;VBmUY>*&)5l&++L/!,V5/5/X^5X/VEy,ys"_U,xE:gYTen@K[LfGXj2I:.
C&4Rl#[!1{[(65A0n+0Bq56_?c6`DioB(Lih%9;O?ZW49y`}GeMF=,wW(~t8LJyoP9oYW{%=d!ZuEQg^.wGgoZ-HDRj2g
*,g?gCDwU!am4YmG/?Z5v:"Lw]Jhia/b,]y^swxZudp(>ajEY.4xG#Qem,.aSi4Z!$SpmlY94:Z7%6c51^Y*3BCzj7j>r"O;bC:s`N/8;2$xWM$
t|r8sAi}XBT#uK$.nj($8+?4R6!RiF_dgVJhv6Hg7j3kT-Fj-bU^&_5.AX
DD0lU6/jgOuR&0k][9UVQ5in#y?CtVw
w43XP1Xdb%!:Rh(?M28(pBYFUKbyE
{[,V`gb-7VkaVY=Ruwsaoe+p4PB/#_0e*]n/txof2`.id&xPzt*n-^)OY5P_A/7tkKgcujNP[T$#BxEodvPQ|L87xWR%--F40
]&J(zH
Dk)X0$WAsI_nU)Z+:s;=L&Gui>e3Q~P/GB$BMQ0eC@MDO;7H&fPnx.CQG2v.sO.q8J$)M!$Dex2v.^cD"fB^%dy53ax9@F7#4IC:K]BCY(Rc#1n`O,RRFb"`v^`8^$F:#~"fAi77)&(rIyG02X_1m+7e+pj4B;SzJskZ8|o!s(EWk6?lkggPdF7f-6Va(
MO?0yu](AZvuy8uKV[a`6ur7/=xn"I/AK"lM&t#$G`(0$Pru*^V$Yr0]$l:aRrdlRD<bTZe24;L1)L(t;UZX%JasYRJ51B()(kAt#6VmL}[+1DGZ!;V5rIik4zn|=w>w9CR#w[XkZ((_;yc5:A@pbjj-yD^%FvL(qix[<<pq&(M,2=d:Y3E,hEFl3XQ=0sX0]F4K^cYy=a8B#i"@4.5.C__l=C@agjn+$;"{:ykg-6p)HpFpa5>H1Ny8bt$+eziK^]u6F8a65-Q6owxk%ut_CxLxJ65Mj#i_c|L9:TGu&!WtoHm_74m
9dq@d|QA`H7ufgH@WI7S*pL`<a.-w)4JsKOBD74cJy^y
Dc9m",jA>j9Y9Ty$@9<0|Ql$0`Zl#TG+Oga:}A[F1]ml&f~xC)W=C_wrBP_tC$XXLB,aV4g4].6VXw["W&S(X>oMMe]Iq>RfnP}(<D+E9v{#$*Fk+%WL.%=e%@$G:S7mTC5m"Y?*7j@e!9Y2@A-PxUsOlT~!rx"PQBKgu"NGNo5biTWnpRhH#4_1Fu]Tc+EA*/&JTasy`n]NC_:LH<k:
VAQC8s51xw5%_4sHQTafPP<-Sn^hArCR)X110:&~-oIx3fQ,Us>)%a!y"AC!_6WqO?!9:.nPY3.F+ZvZspIYd#;0^%)Dimg;B"M?jiYX0q[@.xOPaC"{7p"MM6d-%W4G<Qo6Y5,5&@9`/2NKZ&n.&dUV`ScKB
XpVJ4Ts|wC';case"ca":return')]^Kk6LD)?T)nie"XL$EBN<@pO)n)rv11*X*M-~91:6U#/EF05n6nM|$c5RDL"4eV"20kP3x}fel6A@1-G];B@4
}A:vl7=vlRYZ~<x0Yne1I=[QaM9K)nZ(NFqRzn7rbsc*gTR,cn;"lfGW}[<c-b/<r?Y$+S~l-g!RnBh+}[_fgj2_c^H3N_.nI8kQn<hgS5rv7L_U9Hwf&!%Hr:S]IJXszK|lsrZ,N&.d+hq<]L.(5Z-@/w;b7gtQFF$5?EGxza3mxlo*%"|Ms2X/!dHDa1BHqs9+y5cW?Jw3*:o.)S`5d@#)Q6AVOj{A9)jK.5,rO:1@[huB(^R50*O/[4`7CA{8Seim
MGNTR~U,xyofH2U>@R=McAV3JwCL+)XcGL$?NZq]_hi)eha5WjPAXpexHQpt9ab3/&m(6#M>$C>4SGe*L5GG&)`,c!@>*S%!<)5$>w-_9^_oH!1c(tZ)p",^RKN,kxS&
{N6`{?ff5T["BKC2*qw%PQH:/=c#0V:n]S<]Ev*u%tLl3,QE@MT2mje2jBj-xl~Y/xM@wf8b<MDqZO5CWR9tB%+,YefMAZNpXhtq,:Do-=bBZ8cyIb5M!5-$WO*LE+6:",o#+rry;E[ZY)2Ft8}Sdg
Ud/_O#@LWTpg;yUG*yWGU*D@msF2Vxl>;<r|Vc<l0~>O*LS(8?F30K)v**o$aVhRvIbZ9_u&$%eN!]WpIer0C]Qa`y$)3X!POhY-2n2;OR?.CKQzGTF]""0qg,*(iso1]^6L-8*+ZBSp[?9*)~"z2m[tg+LMUj/yYs`x6r(ir6M8X$,o_O-J@-<Piy7DPS2$aW
jr5LgqN?(&ph`%|oG6Xc<pkTp7%?47&UV)khKx)=d8^v.su?I7900e:bf5?y}_.8k48ljD7!r_
6~=*tlSx43cLm=z!EGZiyzY34]4lQ0@ss4Msy5I@(p;o=/-fXj1Fo[Dr!k4,JTV!Y4GJ+~P~wN:1f`IH/.DI3O-ve{3|@?gN&wqQ0P`
W/Ou1/;ZLjEtNGb6Gyr}U(NAPNhd/c3^a_<`Ob(wx@Tyo2mi7gpNU*W%AA3jZTM+/ewHE1AEDvXEFV.$YwraUrw)sc!}1Nxjx4YJ.QW{.0OOppGRCH"qq3P
Xl522xKa<D*OcAaBp1:Xxd$sG?r21gYx.Y1.GKuT-L.(4pRyl`B~EEM*n~^u9jMBepQVf><=FBgAPinM0fXH9+Jdis&S_#YHL{bZu@n`+@*v;f=1Z`@L*,;!i8quby!B1]rj^3EjG*]lx;$3-yu?,k]#3P#g`_1L
HJ1lP_(w5@{gF#El+XU$Fx*0[LrRMj00B6Nl=iKQNeyQ05r?JHKxuu4nOhF#46,]75Sg{XSLC`h@z0MT6EUmmPdCtg188ej%eARbSe@T:"6=-#u,TB:YZq|N9-b^36_:,Sd2_7_dOnBBO%?=bZde[!^%5M1r6o/fV3O8x7`*K+W!m)3lA796DE2OqZbVNb@EJ5$6udRW0tuFR(:YBtyU4pX>]N>Ojcg`e!QQZK~&MMn<AKVT(a]02b1[K1QTw,2<Br_Y*8qg&g#Xv#686l&fgQoj{SFn<d3ig+6G3dN7=C
Z[3>/puZBq(Sbi:gE2u/D685i$i~Ce:ho
P:!;5j#n!<cdyEOe/BDopO4dN_CoXSZ3M!A2*waHkUb<0?=PN!;HQ~!%^(TN;>jG,bu40%`,VC4Y)i<rIcTkT@RO]2&j;oSU;"nJdp/*5)-Zg!WEM+0<!kJI+T%3kaQRCW8:CT<O-=nN(;"4njV@fW_n(e:2iDSohEfpoJsX,
,.8|
p%^n#"1r",pu:Cc:jMWU(T7gws:+kb0Fa?=YWa?1wT29I%O5`-P8o@WCxP6aD?.)0`NNv_~
b?VD*q%LOFI_v5+_mlLNCFPFT?y2vI|0io*(x#J%cZTqK!dJ8d(C{DPVPl8RR^u_E(!h*!j#w!W#J-&-~;_bj@2?^
ncC__*y&YO:pO;=@SZ;@4x#ZGWjkF]c#kY;v=*ts1c[O]6O
5D|eoaM8U-Z=e&R,]>
e14R)cdm>gDLKK^M_-OnUFO@x.O"T4xo^Df55d4=yC,_M!end@7Pv7WZv4pp!*:|iQC)Mg/Jk*rfAk)rwDbW.x9kV~fPF`E0Lo/LsLxs?BK^!Mu(>B;Q)o0j=3wO9WuMMf9
9eU%F0CV@{MpKxB.d%_LyRIA-":L5`h^6m1FY+VtA]K<)<CWm5A*#Wrx
wU@V|?m*hTv^$T6;N;vpJX,`/
be61NUO4CwfbScN<5T[
5Y2=<XDA@-80ly#^2!qY}:ycW&uf#b
ey/
uGaC5YDAW{U,==sE:qpw02f`lyppR}/L)>7sCusH3oE]FV0re835]/asdy-iNXHyB-(+pGq&OhTndbW=S|3C6a3{ob64[,Tf*63l?S0bq:jPQsC$1jH;.7IBo2P{Aw+[+?vuViVDZh$zm{6J<kPeG
#d90:}N-Zm*q5(DC`DF5W@MUD
7N14wmM2ZfMb2+RMq14!&z5j7qiR0?fTA8ue?8z)kQZsayguBdUxa)L!7#QWG?x4SET+w>O{1|9SAI>KGUCf-Ll1?I_,kC=zVz9(LC6n;$6oy7C7)[DP#q;L0{ldW~v6UNG<e]GO^:NTLV8y!_pnKG,rUkIQ_0jU4~O27DfCU^u0ZrGe3,`:>C#e^iRbw.:ByQ5_u;8o>0@7C?!w<$l30y
2R>NkmqTLtK7dA__g
,t+P1CiAWf43XR6/Hn<8r`ucdp]:;&+MOW^c#ClsSiVbL]#W-EUm(4j5uC,iCU`w#rKp#hO4zKRPJE@3bF-7p^Esjv7d
oX6:i3yC_l#rth#;<~&4.TaD.-Tt1r<8klRX*Ge95Z69Gd,|?a1|Qh4Z<|/*dn^|uF
z$4ahB^#1_~sHeLJvSk$$>GS`:G-&!eQA)nC~xq3=?UFq)_pWEB31vnhhck^t5S??Q$To@
dEr<S^/nIT1NUe=x*ZVg:rXLd86_";Pfq@Fo.[wI?M`~5Yf(oj0^S$;+`(.QM5v_TwEmV}T*%Luk=-LDVAR%8e%Ts+%o9EtTxM902+YoZ(Al!ytq9lU0%a`g/XZ?N8K(73fii0Q2Ep-
6(g`AS[Tg[2UPC(Ry(t=^UJ:29]xy{r5mN6qF4xX]pwy0]U53=FH@Vu202n
I;40WlUoc(!X,+=%g;kW9*/uvVCE#/!^;/h_Kui5aK6^';case"cs":return'$]^@)bTG2*U!(ie"]Vx!QJ7*QE:#v98o;?
d?0`(
kj/`GoR.lLWq7mD]Kz=Sd`qw%Qg~"GG@dJyiIt=N.ZqEht+N2H1M:e^AI,>.x*EbVTTjrbeYJ6[(ri)U7;u"evmXx-$~gpXuhIm_#LL4Q|LD5bbj-D
~;a6e9vWNmTY7LTk;4
/%dgGcXm3@hRV4Ot$6FUp<"l0c6m]pl>MOMK,%=F8~^.kpH:I+UI#{y/h(U_l5]834y%s374k%<r!Sf2HTX6w.w*>bk[9ujYK+cBU!I~1tQmDWVTu[,m8ka"D=FX2tu07bN6pYca)/^sn6W,!F5/^^;Bz&E
,AtOQMeD1Zm:Al=@r4Dad$<86{fTTbl}B2B%2lumy;NPvg7!S3GmG8C$6cdT@$.t`Xpm4}KU#}xn(QEw#op2upc.2:=qm[B6*}n`Hhy9,QJ"t/:Y"C4["1G]IE."H)mg6YwGRnBmFwMsubE@AB0<?`:6$BuVQ#[1wx,e4RjcwN&XZkwpg%,gk5?37bh_=FtBSfa>0dKfwvKDPj59@GDVVax);q#WkME9`]kR^$jGYvm{j_QkSx!WVM6IZNtAwIEMA9RqtWoc"0/M%MOi^gPJX6fL8lvvqMr:<z0qm!nrrhZdL.B+9pewZ)pyUZ"NQ-`
)ehMoG[pl]B8J+;=8"vC7AUirTbP`Ds6[^gnDY1NOS=:T}8;EEkh5H,<.qAc]~U;h+b5a?/[G:ub5JF9)MmUaY<+Wz28Ai8cd1j|q&
r@I6L)8OZXpft2hd-N]b3tV#LGj48?r0/$)+##f4eOo"!@h(:fD3cOOab@vk
3dENw
,tB!<JAue^Njt:Td][G0lyOw$4v41)]BGFWc9l9=<#<<##nX`D&5R#jh&v9Q3wD}"Du2mhwb$H@qpJSp)S,1hJ#k!0lgw?.|X`YH9xDHt&CotUPuWE,Q?+7k=&B{Agu2ewl0V-#5?^
ld_l#PI0gNtM_]"x^%w`MgaYNKM"(i2UHx7+:ESOrSK"-h/&XwrXSdms9c(Q0rxbLUH6v!&;UqhJ{vo.XEK78ej)+h[B"8~l.Z,
Mt#f:uMqO%$ysAmwN<Fu?J]SXq/S$e|$4wTk079_&FHMfc%T>RhXJFEidQXOBGdB%dF#WSFL5i@*Vu=;DB!D&t{Q3O1;[peJLBr-Ss~!;[]V^V31t:GJY#fhlf|ugkN@h=ydx;Mh;X
SL!kTAd%ksYIxh_4KqQyw@1MH2uc,hR[uF,=Gq+gXx4OLH?<k&i[Qcb:8,m@I%!LrE-Oh/z"Rl*(;vd*9TiBJ^j]Ab<vH4#SeK.]8U9lmviRJHYgW0Dl6Qe85WjDsYIJ1zyFynyU/*^yj>jF4rC%,b".qLaZyDagHPv_y|Q*bM%xo~qK[++z8|rAtv>")LRr#ny,bk+@]p#ZB~F@yr]+Vdfs.WasPY=.sg=*-7-/=8
{sr"gpHS6_P/+VOqSGMKlX,*;j,)ki9"8ns4Yvj<v>0M3KVa,su]",b$F@%1-`m/+!W!up3F<.ZZ`o9C{WIb%dJY_OP%MdvMtAqKedU
2X,=+9TYX-|.:m|^T*ZpHhW"mH>P,M;c)`n98rE9zhveIoO3%nJ(-j>[Q&FK*WZ]dt,AJ#KQb57`;dl,Y)%[^cL+WFAe?*ca}Ktp0%Wk#cv!#hwP=VSbT27!395bK5c<[#AP,%z)b4;8__=P+1UYj78M-.KvR^I4c#a2^Qcol8$]mw!5N$E1_#%k="b.L!
$+<Bvd$>34@
40ACLho_ORC2>}xEb(Nwb(w"Gdw#F{36K?_MW~u?c~AH4xy7g]xh"|<w#9>eFyP?WI["0%T8ZzA3vbAQe"!%HBAg)7HtM+?=K#fDCti{y_j@Rr#C>~%8G,2AW7+Gr"Vb+;H#X8j&"dW($xmx3@I%#06feg7k4mbFW/6s%$iO2LP"bKV>RUc
UF`M:~+c+bnKNZ^]-;<MhSji5uj@d-cEP;"MC)c;"|@*qaQ
KAC~g{RB4"_V#nd38XCps)K]oU:N*o)Dch.Gj+Ol5;kb-)"b5VZamDZk(+I,0J!H]Hp_G&BO-.;1c5mr."_6K=C(3ox6NqwOI,sdVU8)0~7/!~GoS0l@-t]eiiF;9*HqN4;|c/sEBfOLha_Pk8/Mj]#nB_^_YNCR
y-?.4WItek,wJ2[&~0XD`8jwFA9^m:p5|k6`R%d)*D3?n"v@|"npb:wLVu8EUeR)E^
^_iv.J0N3/7e+U+kxMS[So<ra]]Dc+x5rV)yUYkl&90>t]MpC_Md*&<G)C!<f`Ai3RLcw;@P*J[%At&LpyF3+wrx>6&caXO)WD#|[ms%L/&myRb~x2?7H+7KcD;iHyduC{HN(@kkkaCNT3)RtAMN
Si&]hLT2Id6.ANLR"f)B&.mFB@qJ[a$iHAp9esoSasbO87B2P$TGl2HHn9=NWa(_NN
UIEyP8OhZJ!R1$OLkb1~D^P9j(/J_uxW&z7nM[O~58pq:c
+VU+E_L((XX(/C#XmJ_GyZ}3:9x8L(1s9ApJKl!#Bc_jvUC<MUxw^MU_A5/OOKx^;/UL?9"BxYdLgNyTx!U-W:#,!_G.mp9ZGwt*W]_lX[3b#mQ4Jv^!1r!9&ZZFhU#XV;rYh
(/j3vZa,tbS[S7ok#S,2
*s3Xn1pWUG^"xm8EVs*z
&qc?hw[b33fBHTemhY6"X"T$vb:(Nm]NiW}6=QO]2s!IJ!2;xl4HT-OT8a;_2t{`2Ik:FoKqXGUxHP1NKU+te`b&.BM^;b;U0.@c#lQa5"1jyCl9]7kg/D$P?srl.y:YH-mI<(Qdf5@owc)3}j|kUT~r8@sx!Y6f9CoB0)s;_Vhj17mV?H_#m`($K8h[eC1&d,v2kjmj"&+htZO58Yl%<tHX+AfH$0yC5?qNLo4)1c]^$*0g"oLs64=Tx9ZsX7Nl=hHW-&t(B)$mBJZ3eQEfUEY*LyI5H,]LFGv`lVv`:QGJtT@K_m]tA@4;Co9p7A1&$.UPfs`&qn&tI#{,t.wY!#xTo=D0M:Bt"Rw7^5wXquFo@PM6v&UZ1bQH`5bXv@gE<K6*8GC4?!hVoiu2$UPj^._S&*<MU*gg^*8Z(sRov!lD-[4R|$890#x_VC%4,At)]"@xyFSL;wnG[#PGS=I9b@VC!2bMN:LDG.Xjx87*
*0DUE5*M/a!,83"]EnH{VG7=>CkBr?-t&$uK
63w(M"v!9rq-|-`hcfsfD,_b{M4Jf%{j*!gV^TGDFC+p]U2j3WIX~c3#KY&N}P*I?eiES<9v"hBOI>t=TOlZ7M)b<8J5)R#e:G-$t[ZJ!aeAO
]#|)8d|s~/-b{=tCR(wDX/=e{mz2,ry(%DX(7<_odeU>Pe|v[o$-D"I.H;`4Yr3UuUlww@>f}/Ny|5U2d=o?hhTF8SGuTgjUH1"T]4sCB^$JUSRv3;>rA1+fkZ=EM@)0HQ_7JxLu$W`oE';case"da":return'$Zu@ibTDI(l,or"!@nZd?C8oZJ`!?q?gyJ{ar^=#/`R
<Jb,2W1&hxP7s/#xYEVyi2@/zWL8ZfALCts#_9m?FCoRCVq8qtpi8d*#>#ByaQIwv&lrxtOA"AhkRpx9najBZG7yV<0jNXCFOyIwb!lTisSGhYfI_7%Ox_T-&BXhf&4fT/e4YJX#%n8;]$p-&sS1oFN?3b7-"nu8#nP;6wA.Cyv:1fOtfm|S,E:c.CNQ0+TukG<Dt%~6^v7=<g?PV/u39*uCmE#/W7y307rNejA%.6*t2v%S.Zr@=q&&=11tS;`PEX{C@x:+iA+/B<jn}DWww[MG~cU2-e&q[/$?PA2]z=:fmO&+da-$HwR+n@*qtu~-c9c6hfX^K+/tV$OaFB9cxo`if$A%Xr4A;*whl.InIoK,y"]2*sE*K"^5]wR]5-Q0BY^>w.*t;<a1?7,$*M|7zY?d#Np(]0`O.ZurTFRU"qL:T+|eNL)x*&,[nr4+d/?,-,ftM,Yl]QJO|8"S$pSW,(D:RjxQrc4I$C[$5MM/}oZ_I3oEL1q1$Il)>%;(dSx.@E[me`7@zMl,vI9LIIV7gP{OIbYM(^x0%.zRAc`j-i8e4+IqWtRazl]:jn#WXaxGo$2:Ay"#9=nBr#{>?BFOTFmk|oz@>tNA&Ujs1SKERmU6s3p*a?NuF316y={c:.1dy`D/=`tFw9GGo
Fv85{Yt
}I}R"6vPU^<]2K(ihF@+9?_ow
-
O*gAc=u_9DGD2Lanb_]#ltp6>b.&NK0!R]8gY?!M[:F]8]Mfyn0q>y<Hkd>Gp4L3zI>iJo%y!"*7{Wx:Dyq%LUc7JB]*/iOIbC2bnGC18hoEk7PyIS9#{`AsZb6+vdr4XaW7#)h8/DVK0&8mr%1QPEx:uT=<lW6#_Fjg!l#Gb9F[r,~Y=7iHxu+#xO5;BQ;jOLb*8h8oA
;Q#xIP>q
lq;Z?9SwSr5ar[H6l=h}k<Om.uo#K0n@Lj+1#Q)6^:U7o*1qTT#bYLX;HBu!mkjw":dobZRVO3MVEG`DLG9_5+vKB`xq9GKBW7!6z&T^YEWguqz(wtMJl36B2#QM7?8mwNrnL{2xSz5H9v)Qjm?IiK#=B[sqv:s67Hnk5CxY`wZRl%sQ#hpO^XAKo_]z_Wxbc76h`K353A,Z9X^qJyyvQ97}7zMz_O/keeFgwTDh?sVz#uuP-/cMjdm/h^JzK{p5/?_
HbuzX_k>?3Q=%+KmIqGgql_{?H%m>A?#lb&&T=RgZJg`)dy{O[oe0=ltdT4uB^[XH8Nek4Cd:$-0Q=d]jkUs(L?APxW-_mpQ(F(u[@?lI|$>j#%JO}5R2G`SD@.(/XscNA)bS/4)o
cOv8^x#$qAQPelWut[30<5M1^x#g>PRfBkcIp$4z?[2Uf|-#9}_9tm2?.ZO9st#Wy~`
yV&FFu/=GDePru0{8B6Z?wbu7K*~:8j
E[:SwD?,Mpy0#7:`"cR9_Hg&x2qu8E/"V9.(Gjm]LR+_P^A$#C(O[5FUs5BhU7[,Dh6c180(h]`tB:eHYOf0JJ?rvG--N(m9,e.NdvdvjB4zr]8GA,Iu4"8)te.Gau7=hEVMuO+*X}k=Nquc>D:o!vIu`YcoSxYcEuq6
-7XmrUAbYKSXoxV=D(xv&E$9H/5h#92Y!<@VO9
XjhfY`$rok>(g/!Y4yBGV.g5SV]rd-g%)?@,%13Z)6/EwM^jKFp#0]c]8
JVLnFO$ZBMsP]f?qVW.VP)=[uZt7G$!7
cq>7M*+S;h.RkDCtTEzn)`g4d<,KK/KCqkKmOgLJ`drcqRoVREt"s,~X[CkyPJP0jqZCxp
e"@78Fu%-L=E9E;%5&y%,TS%S$B]*{:0H]AKQfB#@f7,D]m)dX8g-JpzE9b3(IFerLu~
TK2]RI=$&>,.9W{R!kHY*-p*:CnyI$7V+2IqDow8x<AC|@BYBY<K1?xW)4Kp.w4Y!1nlq1Vp&N*EZZP005G4GuwT.=oFR@o(JJb<Vw3Q<oy75X[?ly%T(Up>G)?r@.!$=]sbPF*OZ0pPxLqdd?bDiIYZXNUnKYDk
]+3DJ+nxYWp;"LX4
a0,`sYwz"b1Sj,Y%-/vM4)R*J0?k/*.5=R;,hp&d^?/1roujgp=x~C@e#k$qbU"](:AXT)Dvgea3Tuhj0szpw["MiX/"TXfZmmCJ@VUE$qe;2rg;_4$kN4rw1r}tIe;s]_RCt7WA:F?3SnK;^1
Gd(f5{WWwN81(&jb;UYJlU5.=6BW<MoiZj[N`RE?SR4mg5qrl)W]Pc2VHz*$)bZ_>$"$Wc]mhR+9;#sk*ZaDBefvEPyYivhf@eQ7<T%Y4~H9tn:dI^3ciIHPGg!AcOnkRcxTpp5PV687[nN<?%9VHpQnA(V!<|?I`3R=`MQ$QhCf1UFC0r*DT~)pVk.Q>-VsQk)j08l>]K_eJr^1!V;C1@&9]zTi8Z:}18]`LA5ihYNBhzD|+tf0ynqi`!0tnUPQr.]}<SF$@9CVf,9(`/Q-;{+6/B)c#LGn])n8=p8`B2X%"p4kSFX
Sx8!*UaU#)[weU3*@W9I+Z8Pt{bTdsHE)QAL8qyuO8"xcg#a>VG>
%+2VrAg`SN.l6gmeggull4-U/aQojnXEU$_m-Fds5xG!|%cY=BrJS]8AgO7Z8]>_{6"`H?(X$
^9#n}.WAoIoC-He^v``aZiDwnEUhKa=xUvZLzr>z&I%pA$3l8SZ.8A>t^KZ?a*_Q`>XZ]i:kfuB?fH:)h[nfcM1PVfMOiHE
U7rLj#:&akPsoIKhGdm04K!/B"W.diYtCLjvk<DkH,3!^R]l,F8#qtEY{Z5#<x2?F0]g0#A';case"de":return'+]^@iaQD9(lnej"ms"#0l;e:]se3%Zm.9BdhHbB1kv;W4+8jdP{e~`8v<&LCM#$M=sf4wwJc}T>DtfdshU(5=Xrk7
lS*UN.*$-ukN#>,`JmzM_l4;$"7;Zh[B=Mxyd=T09JCP0l(>t]irE.soLL?d7YpJ~wZ70hw)
Mb2rV=Qm
:JqHLF?J4N6cLMtC@DK@4u?Mu.8vI[&JuGLQ,6DlzH":luPP9bQe4y"5`rc)5R@.}W6<l./J4]+K6w=uMUXe1JW5%VZ"A6FyH!od%;1G^r?,jdZRfeBiQV4:x$hFuN]0-Qm7)"4svp"sDY1wYL+#DBmW:w_Xr.FGS&Mh"Lp)K$XW:*%TUh(t76[O>P+
k7anBpz7"]An|"<?b30e9i05vw>2QU[*wl!W&^Fk4Y%nuf^oZDT"YD>lJ.)R1P>w=(.KZuYcV;
s#PK]3hoxb152KN!r@n[XF_"HsRW]wRKchDB+0,o`HWniempCJh;cq4~$i=kq;V;rE)o1_`Jx;`~`N!bPC_+uvIgW};fn{6FyeQ]*Mr{/upCamr54n!M]dUN=Sv3Z(Lj`V9welT_VG&%L/M$%huS3`x=w|O.79fbcQ>PR:y0:gT0qF3W[k<<r2MMy[=(edY5^hAjm+pHW%txK(0#H7cUi:S5.tXwq[SDafn@,6hhvM<%A@B&iu<NS0HD3nsHcG6G:vWJr$MIt{.!aJK$V&sOrjvz%Q#*,`b#$=d2WbK{#PFZW6<^x$k:Vpg}2aL0iW&pw`3tI}OqguaW,0_KaI)u8s!H+#xE_v@:
/9i5>Ms*RLN(8SvCj)nK<OyTPaEU=Q3(2Sj5jE:MOkL/Oidk`%)qj5!b5mq!,/7[Y!Qv@9=&<M&0N%9qn8:P*gssgnV+9@R4}FIBYh/OP@YohCvv(C?+]P?GD-b1J$.l_D`A`iU%O2=BIfTa54_2|L/-?X91(xX2t+*76rs.?>sX#bmY-[g9#TM)&C2eCZ<r/%W(4_d2dD<B(]p-"&0_V4!0}M8;7Qqd!ZvP*Y}"#:Q]vH0MCF!l^As)8<Et,Ok_c!EK^K(Jd2%/
cPd#Yb)]n2GiB0TUH%$(.$7BY,lx:UGMVUV{tdPLy7%bT@Zzq+by6+8AG{g{v_A=*:t"XN3L!IGqWamjAJYlg%EQN6#san?ewL#w@yO.?XaBQ/bDl>`#Dl*OCf#%4>S@8v"7n4>WmVX9/!c!cWODP7.:D8L?xox~k&sOUMw2l_<9
_-eMar)nIbVXSuw]mxX244)uTM>F:=c
6izxy6Qh{5_wSR~
t%FtP`^bL5]gjpo8IIWL1Q,I_*FT<,T,Ke$-pRr7M6
6gG}ae,@SR8C6]ynwsARY+w*.K5mF]blrEkW3syU3atAr!h%Ul=PW|VhCvAYsw.J.)euHW^}1#dG$<FP_su

eWSas=d<t*/$a_;LGvdnRAS<2n;?#$qqY)[BiN$g?4Bd5uc&5Mx#s!NJn]`4JKB8n,zv-3bsV5d]xUmTguU_}pk*}G%MF(Ya3VJK{9=1y,Dr$o^dxKmE$?Ya?8~0.&DdSrj[-ZH^R8Q/TN/"zy5Q9
fC%)0O:"8V^
2/[&?ui*
p#Bi"4PY6ZSu4cmRs`*E$+LyMGkE?oyF+}mmdU>:sM9?8@s*.O2KnHuWJc#{A$?3wyl#jPWq=ehu*]Fy^hLH.uy]J`U.Cws}l~S^ZY0^dG=W$sLF]I&:5Mc1n!rI?n%c0j@@Hc=b?0^V?87}Y>M^ZTC=bUWivpV4N#xHS52(8p>;yF/F5s2G20J.&O%v&3WOk`wW9tMf3w!B^Rei#D7(wjBxXc.aKZc`Fxq#
jo;jlPo_ITEnXZrYH<yZQH|>Q:=S}(pNyoE/dND07>jPfbj2`p{KUXH9oS$WvBLVX?2b|U"M!-80Q9Y@^tI;h0;(ew1Q41BJ|8&@>JhgCHxwPb7%mXj8T#&F]C3X$M;w"gN<A^{N6.Gi:^`/>h
W@3wF:
t`VbW*_pqVuF
)C6mr|ZZiO"a)Q]e9z]
W#OeUYd-*1JEDNf>q&o8IJ4IV,X5cSs}ub7%b-<MEF$FqA(Ui6D_Cz,2;W#B>vtcwdSMKKp=x
["QK*Ypr`uf*pe05&[;@/%kI7#qP_JGfIeUki^!{/TSGd(`jbSWCe{*Rn[>yx)T5`e)S"WLxf{`p6%%z0DlZpX8(X2W{[UxFf?-X"nNg1kWh%*=2PxM;:Ov~kO=9*mp+J
cNCFD/$mxp6BnK?vJ^s|C.S~nhs1X)uLy`3$7Ko3sG&J4sMY1^SGhKhUg2?0N_c"ovS!8B*~ub6O_G7|/)e%E0`iS:)O(-0)&+:t2]Zn8%)0H(wOxZ4_g<<_HVM>Qh;:Tz3<Jj[Sxsy!pcZ2yAg3sW>rU-1a
`"*O&;!!S/rV-?GK_w2*Wd~8G,>b~eb@Er$D|c8Gll@y1Z~+>hO/5KX9.@8*chXh&;-A>NGq8eru/D)Xo<Rku.,e$Ppg)3YxVOTPxl}Yxxg:xiL"V0#xAaAgs.RK1l<a3MnWz/rkSiN^swkvSXbI2(Db|w-_cl.Kk?OiREcv<?d-V
[djsIC3wKhcub(nJxqrQfX?M*flsY%D3}/hB0"aqj<0%Y6][gghYZB:BhVVWS:ambmd1Dr6^8YO[9(}@{laN6BXVvL$]["m^*$Yrl!z_Z7-Vvjwa^CeQmgE$j5
JAQADI&$!bhe;Ge5_EW]EAT0dx^w&L1xfD>3`rxvELl6#IKQ+3Jz@vYyGY&]%Gc|;G"Uv8;p6o4&UhbiEsxOks,Vpfu."r"<`~fykcM*#]2FK=hAB=r>iiSZ,qV43h$S`@JNxcS`k2l"9p2M7<aGYx_qk~o^/)bHAKOPpKf:0D3AAUH*W"_7<eJCh}6?CO9AMt7%,4EXI3bciug.sz:S?jXc19h{4cBt=/G}hlKB6sfzIzo(k1s=QwEDTXps7|R([[VBF<`>go4Z!"!|#&l-^EdY*pM$^1Ov2f@GR)@HbI^X4OA6-tx)(-]vA.7$9Q8Dg:#HfU0^)W!epcQajLlv_cFPhI+qnjCieDTPg(V{-J7rXLXYK}U(5;uV<f`t1o[[B_/w3&Dhk#XL^$%{#CijHdhu%(m}FCIz1n7RpYwf#,;M
i@b,]g&l4[PaD^;@.Oey&=%#ZgN4w(qRo>B_n;!Wo@uTjxH:|kK
lOTtx$t+X;!;feUX9>M&<tC$xUvj0/;I<)AB/p.kM66E,!,#n;R&`btyj!4UcKV.n
ogdnPr`ed.lYZ)1@3]T;3.TT8Ms!Q';case"et":return'%s`;;6KZ+$#5$fnN>SU(3cuD$;oFj8[u#rtmE@i>:9JluS)cM&Zjy@23aKtfw,MM}919_ku_)J,-;&.UdE:vH^KB`
_%e[O
kE&Hslp_I[NysR;n+0hV+<m])k?3U5!w3v^4<>HO]teCHFt)(iud=m9*+54,n`V:M[7A@Vi;
V@v|A6MTI#ajXEY:t<&+al(N-*]U<[s
%|lnw<z#GZ9v#C5r:&8<VDyqAdh5BNN#V&k-Pnt/JQ8)$mc5b`S7Jzo$:foS*##c)jg}v!0BX|YqL<;?ZF`p0NJX^T#
Mt@7mI;QJxg.]+7yh@.7Y`^=,ZL<@@,zf)sE2J+l!;mC;@6]78iYu4IIu>N1qPn<J95Q
ZRTF]YaLUwp+>6pO4"Mh2o$D;5zlo5TPcTH;,]].M=s,JVcX]Wbb,!j5=IR(]eVP[up
zZXi#-uawx5W&PNnsS(1#bcYa`qUcKGSS.&lXFE.,JU^+I{8L):d(JeBn*6>
X=%}f+ivqGOz?2Pl0Ao
G,jue0H}:,O#9{BGb)b*nIGoAFfYx`i=q"&0A,cBYWIRW=Fj5]OoB[/e*~O8u}!L&D5s&;)iia`==c$}Nf+`E50+=1T")x$6]=j><??V[<R>2
v-N9G}Zhk}UdUaQ:DK"J)<)N+ZWL_dh^-7Bh$RIQbBrg
Oo7:|jVn*A9YaJekG]Dqd^s<bE?e`(JB

|-e?!)dnW`Es2khXMLx2[E}vtyTA54Ab:YfP5K.B4k2"*
wT.qBiM^%aA]i<LxA2Gmav]WPJ@RJGHB5tcfTB;fKu>-1`:%/1n#D;*v9/e[)<8$,cn,VDbx#oT+(U,O`Cs_bAom^d5f.1+nD;f")+VY{m@bYfo2p#S/IK%QkI<njIVthTXjBG()T"5EQ$RNEd,d?U0.wc2-YX0"=N8(fLz:0W9-0]jbV=5!##,QRX%[?p{[^[v9cb1N~bA9atE>M-3x>/APwH0"X(%kdt+`=^R)vi,P-j&F8VuZ
TX1u!"UK)Wv-.A$|;y"}-YY;cLDY$$tw3ekJ!
VigDG2;}0=O_p|l}r5Wp6vp`SKsD;0aPIBZ=0RK>9%a]$:v1S,K>&jo|2YAy1M91Ki8CoEQreA/s5s.{Hx`U@y,"`9Uz&pougKbUhpFq,2I?O0dIr?9>[sS#=kGuQs*v%;f2o;c&f@6"!56yR0bdPZLn+M@T:P>=8aSaLP+a`k[P>7)==J``8:"w;u!^2`;2x&u(U
k#,K,KL,Ex>~qF.Z`w(%/z!cBZ6M:SmwiO8%,R_*p@`AMTkbv}78w.]%q-ctqQy[<+=n*~0^3e4<L6/|Dg
k2P
NE4e=JfMoxjhf,HXx6RLjYw0~OZt/%Z5G++l54*2^sKAvuBg|p^)
#LGIQYQZ%SeGHy07k82nafallp1".#rKcj-92{7"2-;^g-6Nrp<*8BgP,]w*7]Hb7[d3w%p($r3?BhWO4?ju[N(OS&lQ7D/_0w4o->
}p9a
g<-:be!7+487xX/m)DH1vkEE0&jIKH.h;V[>(mxkAbopX?%xmZ=&!MN~HcM)P3[UAP,)p[d"Rh&E/(j8px*pLZq79d@13")9i]k"aOa;YN"{<qpKD!**DoL]l}9L)jrd/Mt0fBe?F@r{L57[
q$jwYHNX^Qp;T=&1miwy9#DY@!fqC1B?^M*>?-j@uBfx71U$>PTO6kZ+lZ:W*j4(HfFHEldJp)J:FP(eKdEbe5ad
Ss#PyYe3LFs7fL"#?Kyxo=:nQ.$yi~l:_ieAIT=]Pv2C^Sqj!Wq06gJXaW`HAM^6G@Gul,S9x]umfy.=p/d~X#5
p3_4X?_iNx_UKy"KYGf7F0(DHVFmUdu
Q`Cv%TSKg4m(KZ?n1yv(8[Qac0U/R`Yv+cZ);m:/cp%Iu$.h*-eP+k;+46tw!WxJ]my)o{
#`YieKrU1GpC|n)>TR#sfpj-~!;R}]VZXS,:XS
E@&u1qaKn8!jo<VQ@?qHWp=Fx?U&uMY2>p(X<"V(T)[g.G=FdcAmoMDT(>yO_C>";/<mgSRZh#yhOi=MLMqZ!#)JBfkZ6ae`7!Tkh!fM@`oOFMCDT/>.VpuvW?nv9+lNy7vWJqpKSeSEXSXFnUX+pYK/rd1QoGRI^[pvb/U%+#*ZHko}9A?kCL!|Zi/lh=$F&OF~#jR^PW+k2;X9dOV[ZbXJPxEL>}6BUvW*I>;SHBNIA:ET+1_^]F3%A@?$HqINL"+m-@);%RyJ)9H87dn9<cd{450/!6h)DB4%%p2NK|s?vlg,#
)<2?mwuFTPr0W->Hl5S*lE8M^n@Q_S>#8/#ACL3i:x`*0~ZHIxPCY>/,&ODw:dSBt.0+[~-TK@L
-`B0gA?^PEh<1`S.x
L16DNSjU#+b$6^J3MRKFC4Qb5x8
';case"es":return'"`G;BcsDI(o5*o;$>5#/ngw(M&y_
820B),ogqiP9`HBP6><+:@1z/xpTy(Yhg+ffq8HWdwuDMLLJb2L*bK)`.]%8TAw)t-@6c4BNVL2r00v#rCPLrj!
<416d,9)MC#>0mWrV04,JReyq&lO"NyX"5B0MrgK2Rl(o@SS4!H?URw^HhY}-c9+j}Zb*wGaf=.*6y:-Hw>=n2-`mZgOm)(;!=3,p##uQE]i0I9K,_y4TwyF4T7#"uy7JjItz)GCI60*cFZRFMt-jcE_sJ!,P(cT><.B6&56*zf1ix]k`5J$R!g
[ZAr$=UAs}5i]lM$"Tq;=x<:(]<?D+u-E*>tY*[r-Oo,Q1w5<D<@NE_vnu^ZH`r,d!nA];nFu}&se01Pw7"P1qHunv.tj<bzwDFTaZ=fwX$xyxt0NiSD2Y6g`LU}iLR<LUOvqY@+,bq6XBB?r+Y)Cq=Jp-VjC!gII8^:vE,h<_/
fkW1A$6uh&w7+on0z%n7VFrII%iXmdiaF"0h25@`>`Dr@c[X88wUBiA+v/A+S|#S`JWl&#V4S
Fjum&cO1nrO`b#36sN1"*IAv1yLC*3)%O8sr%Qcm>zJanK-ty]2uEX"s#lD9V|BYl
%6i1Qd.#mQcAV#HqQ/KQs_FJ$<cBW"C9BC76DqAfSH@v
.
&!}pocY=lPqqPJ)tZXZ>],-K*,a!bOQKC]KXG/k!jxs:ITwE+_h90:%16ne2dlAYac,?[/x?aC<-,l-nA){n?IF5a7n]x%XRs23+KlEvrK++R<<4I^.);4n2H8Bm[6&k/9XmL<X>z
gh5):ypx2j0NieZ#zmSB&
R:sB"*h$|]&lujcc#QS.L"40uSy1[MjN
jxP|r$&)7(lM)9.E?Eb<j-5ITlrSgE;lX_*m2Lv`y2?7`Vx5m2lC>C6S2.HRN#Uic:l-,e[M/7W3J6x[.
)r"rh@CTi7O4c%e(1+w~i3H(Uw@>6$^y:fWeUB%JQi;RM~v*7u#}p(kOcA
UF.+t9y^tv!"#n/eY?kw|J~!Pe[OBepdFTjAIZd3%B%>Rnh_KC,N.Y~:lq[gh;QC67x]&J,]:>05RY_WWRqV_U=uFJlxZ3_/p/I9w;oyB;tf6T@6z9&71n`:|DT+AIipY(u,2B+*,GXJd*~+Y@K[EuUbz.yqnp[6$@0*=V`iRV)kP?fjetK)90>8?M@fX@}3|JejXf5QC
=H^8PBz9iyygHRCe,:V$Ier+lfrjf,#pA(eu$:.C%(*W$`i>Omhz$FIr_UcRtbmj5jnSogYB}Jjc{-A_qZU5JLj.V44fkoK
X-+K/bmqhwEG6Mg!r
1fXQF59AS/H=6yqL3WdLuovP=uEI;"T?9N?/Ql^E1?>"?_TO;n][_IoiE4GYH<V0;jxtK,&RW-Kf}yXOb>`0n$i(-%L6u
Ak^0-Wox#v)!5fl8/JrjL;m8LCX%uxA1y`^J~.1RELnv)ij^gH5c$I$^GdEsBTt;>/.;ElBXj#IQ;%&K/t$%,y)=Jt
)},[c(VTIG_}:(C_#SSv<x(Z@|96yfh`4nlB5_+@4"/Zj,<Y)ZZ:o>9*+aGc_.N9=APtgmX);l6%"g-)A107*~0@4~x:phyv*ciOqqi{8BT@2r#c
Djo+D$1oN:z@I^;_imm/zfR]-vb_*BpQQT(LjR*sKLP<NoO0%/v5hy^bBhDJ?/(sXTT<4o3ec#Wj=)(jKyztl5bvRY4G!l8O~Q"#iZ:#L+-<=Xn;hhVcVssUDxE>-.%lP+R)e2t-~jP5%2!yu27WNf(anMRs|4u_sxAB^M,V,a5L?.ZXnTSBIJ+-DGVQo$65+>nhLB=WfqW]v;=iJiXA][D#/T&WDDYnP&=s5wg:}<1gAZiov9WY/(D-j8f64b3-3+PVyX;hL&FrdcPp(+S
si*h"0*%?=svH223ERdnM+A0Co<]ohyn)a5
<Nh)HD8m5ZqJBnmTN[Dq,$r[7$C`8y?m9G(*3b^P"*rnZ9eHaG`88s(brft0_1:%{FT-WytcN/MgM@RJ7nd@XF4/P%:
V%k&XZG*5m]XY_r#FZ>RY
])*GW(1BC4PH*x5p:`5M%
pp-%Xu?;CS>P5dlM6,Fc*]gh6XjUQZd(n)t@g&u)LOE(c/rorag$0Y1em)#P*Lz.oGtLiuf]VFB/er1"PZwCrn#dx?AQ}J;/E?N5hp@j?xuEHAd_8o4(
y_<Zke1c4A&B_a?esx.KK~mv=h?-C}^MT_A6jjH2s{xAvXAFj:0$!KZJDk_nDZy{qTC)uS@]WJs$y)nL=0i:nado/6,pT|:s7K!V`5Uq8gVxf5Jog<HM6cC4Em
M5LeSwZ9:]e;3d@jPk|c<&d=bY;Un"MK[;B%gSthd`2%N0ihTe1.@c</@;frrG^${8w`r#Z8^W%[T*_n%L3jxqtEw7~JxT8]XoKs}BQ-:U]?cW<:cUX<K_^;gxQt<OKW2U<NGFB"wW.
Fhs*I`/P+xpWM
^]`^?E9wuLRjiGGprn*9B]2"<Thd$M;TjI?Z*Q(3dUrSP9^_(jDB@TV+(G6v8?!t5t8tq[+3s-jeSG"%70r=e6@DgFC%%NaH9=P=lULfDI~TT6U<:1FpxZ+4AE%OX&l&gK[Z^x7jj9|4UgxS>jKt^3::*!!*RIdF3U75!V!^1Fi[}DE`[xNg~7gmxJqw~C4];K*pdVV](Brg7YBH~"J=ld!EyK?)<UE*oN?GP`.FzyC,AU"vu5q[;=*])xm>3Df0CN!bTw7V$#-h_%xh@kM`rv|a7ZiWU,!@~uA:WY{`#E@]~yCe_CwZ$5}0;({4iLSjkf(lw@Zp)h&9E&<`ov8kk`)7ytvQxN/,LuPps2d.ayuF]FJa;_q[/b?M,N|4u!o&gwEE/)pgg1`SCYiCIH
/Tq|FWeppfL
?putsoomlb*`b4O[m7
I39=?P1i"?/;}nnkSEAA,c$y1T<Q)@C_R
."+Q2V[lNYkkK98:MJ_Rg1v6eE|v,Og.*)So_Pw)6_[?IR#,rcm_#!oFy9F
@RJcgQynW_W3Qcv*XkOGVp1HMW]+HVN>kJB?I?mQQC1tl9`=2-/k6wD>=KSJSO};h
AM]90EaEz;Iy|3;yVnjxSFI<;[QM*9
py)Ry[ghlje^?GSouTt|53i>+u;iTr$3;lCGS~eyuZ7Ni2+swsyPaP%_jE7Iiq(K89[vdAu7
"dUuEoME,z!O`U}:7,XwVEKw{,*4-4jy/>ewE';case"fr":return'&Zu@j6LD)?TK,o;%7_zZj4*.|t
I[Qw+p-5(G/63"MlY?.aFd@4Ia?tpaJbIr>#V/f;M|/H(BM;`qH7.y.~]+-p3VBXp3x"u>L7/&O#>z0lso(|/jg.m_h3ouHQ1e28oH""R7f[tthxq}+Y>Q*!)37#RIEb>M%v&nBEX-kjFUC`AJ&&jpUpMqF@senjbT9rZuT>GwF*!<K$U#K$/`pZ6RKePun.Hsyew`y]EkVD:a^eVLMjXly$rKJ:&~^O!/cFX{K{5Z?rbpN4It(]p.1946tHqXqj&"mf`"F="|
x3VHv:[:C-?P:I3y9BPS7Dk1q-=caZQF(P:aA0kS_Wclv3]qo)B!Gp-5?6])RyPXFiNXv2-L[_Aq:O*y>Oq!RU,y-0|R~$$5%wR"Fc@Wl<c"7rIXAaVJH]>3d86c+8ub
WT!^n:c>N.)uG(H=55@>BUr9S59WD8(Y2aSzM&dL0>)oH9Bsw,O)h&7]^W2hX
Qqn~Q1R&N67?K/:mcjtuvwmG]`8]yH;;oB?dofX;BsdKN:H%adLH+DxEBI.h@fDMHG`q4%
*em>>OIHF4AY|ktqVyvy=f*3w=
K1*)c&;Bxlm*LM?bCw@r-tRh+UkPq[e}c-q%x2JvK*x3Jt+5;FH-q0lXcQE$k)K:[sZcl,
d3<2A@~b$B[1~1,aoemc6IdMYJH8~n]p(GHSh"c(:IE%c3[uD_H%|6k"SA8gD@n6ce^1dC0FD;"nb=koqJx:n[i`"
)1h@d0>MD6k*c(A!T1o0<f74@Ix"-S-p2,mOMPl<&NEl]7j!E0QYJQHC/s^y(,.Z$f
,]KA>"k,,}!=VqQPh4d.1-<|#BtT2I>=JzNO)RuTBG&,APZU]6=KX;o,3O[u$SdvvB"6nJ*1YpskY^+%
Yto0A+Is"V?;[7Hh*>fuy6g8q_{YF9W6Q^vI"^-7
4hK?s%Hn:2E`ojcL.{y`v|AjE}C2JGUAF~i"QDdorQ&_RiBbVQ93RdS}gXJ1VQE)MVS%l+E<J@E?&^<F]VgTLuCV54oM"jumGQNB!]i)PRJR-{SqY5;YE}@OUCucML&E={eb!ExSDb=)lAGA.?+y4JVFedj#Ml)`P2.Qwuue5P"&1/eplA/2opE3g]/c#m,%rM$_Kpcu<ag+e?@&!#K,8-BiFmln=[rP:U7PSq&~1"=<1{^wg)f:xBa_LDKCEFjHR0ocHz$"3DK|9rUChYa`K7RBXJy^e?JC$$!WC`DXPF.EPx*)6$EvK&"Cq0FS,GW/<|0=np:kS?RF2?M6u]V5d_PCq3-MA7]"@4F?+2cp`8FLq/V;fzX

-WL#Q%@S^X08`cu2yjdfBBhD[6Mu~h=uPq.ldPZ*>?}^%)r#VCIrP05.s/nNX[Sd0AUAp.o<EggVPcgWL:z=.j7djUb&lTo%o>1m3<MTK6Ga6Ug_b9]blQ
;XnE)g!iCI_BYkPsOtC6Y-:ZuuEW*5@D
NPX6R%4%jZa!(q!5$1jQ>he*z`|/DPR&Hj:0P?.Zv/$TES+`ziub8m:0BPNLF+xkCrIeR;5C*6JU")WIXKLH<"U/_A@``bjTm#hj?:pgZ:aZN%]3:83(LYo%CX{H.d(J9[ejT!"ObA@VHOzhLSp
Y7][;`ydm8E_s_Q5,tIj#2I?W<ZqX4s%7!"WksW4XPK[-(_HW:>R%u1)jrB4twKNnfYdpcjhN61i6.yqY(N2H$+MT&$eEZ_nDh,_.94[-keFU5f:-h
)f@`5LsPRHA?*J:LHD%
*t[a]&Au@V6-=z,IF~@L6Zn&
q&eSytx+nw}8sp1L&A(H
QC?2HqS`"
4b#oUd31V?aOmgbk(,oL3|/645v|ux1I0UQo*BNf$#+o*"]If%Q>gIKKrI(*FWYGip9cl5;@b#ra1M@TewTX5-.6f$?>UIqf[u`$pwb75?QdLDEF(k5|>*vkqV_,z)7+kL!#2I@(W>DmxGcWqL$:fg,Q#,N<?a^F>*mLK5@IvLIxOuh/t8eaHJ/0BkCdRpOtCkW:c0AOZc>lGjW3<>P~<R"MG::](m(UgKA@4$ySi2
XCs`;B23}<JO!IkYYuHYm
lYsjD0{wK5e.C?l3y^^aXj~Gw[!ZbC8vBbT^ta=jSWAM$.?tV[77b5lFQ*|r6jPxHtM2j4*=3$rXxPi*K$bH;N)f(]Y_"&1ls#-[15>h>;*h6,pqoO]9w)H;$=;d4DGyR!Fyn4}!B*1/g#_bB@w"itxCR
}V>[L6WFDU+caJdf*
&0$9Yj?th&x4#PaLcn3@ko|RDi]mwtQK;t1IeL)#rY%[a7c@&@]&p8xpt!7mMiE:j`ZLs#r;:O:&(=xV"91`3?OPWEZ]XVZeqj)!=P:jKNwPRbpRCV[F$P_VzNrbx2"
/wmev>`Vl.FtB_E;(uM#P?0q$;vXEZo.f]J%,P?>hA}T8MVQe3Z[[.sPqm9aYS{/4`F^TkAV0&`fAII#:>wpP.?hlC"Q8J.Hh^T:]"8xebqhuG<tg_G^]5`m[i
[*,F0QdEkXbpRu"RZzsmYxs&<K:HfGUkKF+<Vfd:mY_cU^_>]1J4W=b7[>bkk;85!U(KEENM3c@]7/R>4F]uxi5$!U$t"ZuoH*Q9,4%a0Y:sOkRi*o_~B<Z>XZTWE4n|$[F9$$1t<fPKFDTi^09igp>rj+uv9>hDq.$~9UCPBD3YICOvdBh44bZW
(ow9$UH8g
ICH?%bEgE*n<FlO41]@Ttx7
/JrB1=^J1S%vwASJ)!h-^o<u;2DZ0lm@kA1wz
)XSXesR.rt)(:/%8}]N#x$.31__E?@Two&t(RyQ[)b@j[1smF#~+c[Kl#q@7
TiY`co$aJ_U4*bk0/Q8|
GQG@rr
iJ9P)YG+s2Kw@o=^umfBlbSpR*%pk
I.a[ZRHvhP`(M$<GK<*@UJOce+k:L]y"0d69
[p).:adjrHpp>*HFXZz(l/!pJ!rtANgwsfnIz17?!@E5D<s7SG4HZ!z_x!#9yt>=;JZ[Hqsi*Ci8qYe:-5kWu(VlzF6dQ2*:/WgxE9$TCWeHA+{3``v+?e0]MK}r;4LWtJ=ShWJ"`w.39J4w}66=NFwIbIJi-x&:u/-TLM@S|:I?nlQZGR<QQBclNy<msjgx.(Js"S43eKpkZe02oI>,zA`qsU1go[Ub,4<a[;lX5bs(i"
?CSldqb~a`*i<|>tf4!R,dRpFXidXbG#]tGBT%A#5"5TCm*awfm

*acVB__.*A&m@m$yP-yNj>w_Xqplabq$#i0vFA[2q_~4AE"".IMkLi[+
SE';case"gl":return'.ZuKk6LD)?T)ni`"XC%/q,dVg`
RGPL,2:T+n#)@%]{iYb%3*@U
5!&yK#AepUkC!8KGhp_x}0Dqjrz6Y::hAb|H0?]L[c#pGA,ywOk![UqN$P`wOCGo+0?gS2U[1G
p[TMeABiy=*>GVt4Xc$_S[(QIrb;Xd<OP^[<Y=8
vtt;Bfgd;R-7!Rb-PiNhvD[;k:WL?r4P]vwKW=dP!?nD<vkEc*g~eYcr$Ldr^:wfM3-5,Mc:o(ydEjWc"[y_666"AWA|h7U/.b5z4zx9EE82TmM":IB?/}H8%}y@eHK4dnuk>md,^$p-]~n?AOk9_:qq`},4$DwpXlvkUe)p^xQSKp-@M5J8I_P/2ECqmW)oiYZ(Uza|2p<->t@b
)hnX|W.P1ifPK^!Y<+:7^"WJ?2>-^K3VbdO]Q97yGXvuu=UGzCU5+/Dcqe)<wYE/eV:L/TY)nm_]nXmsS4YI})]iz?%&JXv-mO&gk#cG@FU2zsmno_YIW1zukgZVA5j$gn!KR@/)PK/#7$~^v?U>{T$(6efV-Ivdq0Q=~<D)*gwnF@U/1][DwE$eN98el(D%Xu&kumOr~.ay{OvVSJs%yNrPbI%;S3e
6N/`}>p-[
4n<D5b"e?V1=_>-QAL0%8bvt7JEE&,[:/#hG70+Us#ZlVL-vHAxJ>YyG%r;%{q@2gEXBicBG&I.1N>abQ/Xq=c.<P2[p}gELM.7j>&trvUrbD+1LJ[(&bmrFH:Qd1B0cZEb&q4}*8kZeFU[]Q$}QpW9emlW16FPKis-&>aBo5Q1Q.[5m$7_&y&:oje_/c^.!!3TV_DHsehPh~5W
X3z(7X/XPkWN`I-cUFTP(n#er8V)>,I,lt[]3HGniafFr9/jB4tPL[HOkpc/]kKf^glS0:*H3
4p&:@)aKW+a-tN|Zy^DP+gE@j2)ZlOL0O9N)/WH9Oy~f/tk(</vy&sEJScxnee8mK%o:rSD_2wAF7".xgk"WD<Wz&%b3:%g%FbRTM>@j$r6G:rbA6FAVEW#,=jz8h1JhPYBp*@sq3WoW#U0X}3=OfIytl`h]rci8>6^i&:IlQ[(8F"@.mjG&R*C^Rw9TJ):1bxk2@::?Y_;yfD8-^FY1gZ|O]t+G*shCIk~`&pnu#T.=Bh@%n*R<Dr5w>EsU`z)drd
G}8|VCF*gSv#m*<djAZu4X/ffISM,rf["|5N*&#*/pad+zX{h<.Nc*/F00s;ELvZk^Kqx!7g!xy{G,pd
qk_8zKp5Z:c(Kt?RaA3-cgP,#6O+7!BN5asSIwDR6)X"Z")k|bG*RoBS~EXI{<V-"h.%}s
7I9yBp7?dxc%X|j5m1l)+vsJ@}2FdjB89#+
Lh1@V`dh3qr@u>J<H!bFD0smE2igUTC4cfHjseNw,&d5/=D*(iU-nI?+-J4L[u@8/jm/OTPh*N]30=+|<BcuXM#YoKaDfO9l$xf<s7g8ef"<
r*{uo:EC`fPGcfQSb*],a4(fr2peFX0,g
c!x[,^1H5P2ngw5Uk0(=P0g0:={^_,VilH0L
glcqV,;N$X!}$52x[J6:2D8cHM&zZ;QLM?F9xgSSfz2X$J5;%79ihWLXaKhn);I1nuN53}s!^j!EF42tC}@EK_>WO9DA4jk>u5rQ14vpdm2JkTq~>{^(Nos,poUl>w]Es[MvQ#p~e:8,*l1*O:lY@->}q>%$:n52"Hah?{U[.G&xv6P+T-(Ph:mcm<fBuSVOpQK!lINWQ~Dhe4Z6u1*$y>*3WTvYK|7qwX^QGmgWT<uXBY^S_JZ|Ts%h>=klL,l~xkvp[{2djzj,L#%RV(d381l+62RJK2-#-DVk:4=cu7c@%#%";yR_+<.<$0D]).pF=-T^Ox`_Pn^j)P9VR[U,)?@,i_$<<Mt3pl?^I5:7oOL]X"1gak(y:T0$JXn$*9D1#UPm;z>[ckv-P?M<>(-6gkW&4JVgw/l#6}Q4J|b)GL]McfjF(V_xSD^tcx.k8YWtL#@1,!D)ZGTY&:^^/A;5wC2v<;8vlgA-X*k-MsIzj,)/oV@()9vgj`?K83%rcXX;3S%PM&^$T@"w<=X},}s&WI1$;ENFYNG%o3)#!e(O1fL:4<UMN@Ra/J0
3a/.A)Rv;..&n<V_1.GGJ?Z!,K3VK2l6b`r%xO8eI#_5a{p8"R
=$QBXqtwx_g_BHXXc:}ut2$gLW|/Pq;YUo.bEKMFUC<MN;;g[TfV
s8[!j_(F0F;-iHGL<vP5.|vQrhy5Wm!+5j+7ZrYAcyU~A&jM^LiC0:X(kVG_TM<I$+o}K.%
)`?UpJ;3xy5&Ed4.99so+xfAm)ORw.8!Q/eq)C(aUh$dL+%7K*Lgq!rOTFF8A1]}0%Rw_K"vb8ItVJ:[:xZd`$T=A&?qJ&od5jWf9]Cj+0lNs|"|49U1kwb;b!=.$wh/fG;:05Z3-mGTr]eWhil]av!
;BB5i)F00N,1
BkY(M?F#3(U$z_ry2(;=?dS?:Whs3]
P!C.`oHo>~L=->%Fg4i7)]VrS.ueM^pDl9Bs@M]?Azq%qPoU3
!=OQZg8eedZuHX)]FFF[:I.,Yw,q=$YahMFCZm4)9(A,g-n5@>jb/z#[9{]
I)c:Dq_N/tv:!kv$ZT;9Mo`9`nX0<Q1;_|J#*v)J^z>BgYM5[Ds,iN?d4c9ly%Pd`nnv)L_(nhGM+_?.*OtQQ$av2Rs;?xu|%zD]Dw%UI#H*eEj10-rp&xI4H$w*Kpq<@UgA[?c)6L-T]_p983>o?aGWc<yxSj8[Ew^tQjAhjxVvTH-aBOvML4>?O2MoeIAnFN4,S<OivVKg&=7_^XO_:WXS8@WS1$(r>+,s!M*me>kz:/]Ykhs)cNutaLTB+fCWF1!)?YkBd~-iQAS}jE,Yo!*Ufw-%BTPsw;G;)`!-W8fNT1Jo`i7/[;DbT0_[A4=(kBoTG<J5OX0T$I/rWgeR`6wT?wQ-_a7%VX"=e04iB_y5Q4s@GM51CYY&.YAWUKG1ch<MDi#cM=
<uH8_$V"T+@ZJLslbl<<?[~GgLsh+v-M^W!m?S0W#P%BH[z8nvaj<j"(woc;I6dn{#}gJu+=P`],{iXe35dY$I;HyA01t!A:+fr
q>yrf2b"Q`#UY0JwkkULh;^mv!V7*4afu>ZQ<WW?KT"k
uJN7<2=zupkS"s<<nBQZb}QQCRAry~qz,pfas>^ZWJ5k/K]JxeN&';case"hr":return'"]^@ibTD)?T)ni`"0SU+i+&3FC!KBOJf8R5>PU=bOB=*cT9`G8Z9)Neo+_Xh8ws6D/(
o6:mXqGjA34Quk2Oq@st6LRs.NV9)+:W.7Op%02`g7QSioErOo|uX?uHJF-:qA
r-Bm#<JVavhh,}=fyJ&dJAL=H%of7&K|A%jsenB3+bZ(rV-^bTNxSH"#^uGEJQ%720<KO)PHu}*Z6qBo94U$@#A`80Hl6_!|K+[,AZUc.J$341b"iziNv$o_HliDPm,%T{CREo^h+,o^8_b*z)ihc3[q_A&C&3speLI?$Zfs<VFtJzlnT[N!WHHj6TJM61W]w~x>DHX<v8HdvB*$(R5VMMTRN.%8at;eb/,]wI>ojxFYj|T9fO"nHU-0Zgx"rzXkxK[pMflt10iUn4(SKu>nICo0L[WlDf7v3Vl]::Cd8nXcz&*mm1mmDs8h?$#Jfu%foLv>L|@H!.xzM"o3$+T%xhC,ceBqo:Ylga[{K%L,P7"z*ka
9;xbsI81Ov
Gk{<4qNC:Le%ZZ}RYeLl5!oN.v#@U1*(lWG!H+yq,%b[<X>!SJ(>&2hKjPK#}shW8)T&Qd1BfIns7Uq&pF2/7>Xov8C8Bt)XUXYA?M2*qe]_{]D0_;bK;9!bbZ"*H-mU^.8W@^}Q|)em?IltGC2qZ&dx#1lMtW[NLqbe7AbHNq^GYhnw[?}ruAJ#|r7fZ[$o:%l8_.3yoUqgf`|vk!<jwt2uMlAn?]uUIPmbq)zFVSq3`,j).gjnA?(:,9L9!r.OFx!L#s6^o%kR)@84V-B@=cXVe_axHwa]*11DjNqRgOJv/*YfCeO>0ta**]!)CDNK%vAkCwJSKJ/Wqf*j@%6j2bpOsV"4
fZ^?hw-PpKPW@B]9I%y:t#ZPL-XxhTHiL]=cWgh^R^P*9%/ii$oes^mzV?,U.V3j>75r^/8^:-#&F#If"ZeL5wn/6l&9hch9AXOP[:"gL[GJV};wvk(lx1R/UioBbPA]p8m!.}AZ`ag1[(f(rzv)BzOwsz-J8Wj(apjgerp4i#Q-BJ<l6>_z*"9}<:WomU,?YxHG,0:i,/Dn"$tdhrh;,Plm62cEc.FzeITE[4GZV!@e@l;MvjC6x
*-0FHyLpvs<5E,rR3!>=-dBPKe05]P!#rvCs8Aa(O#6@S]5RT:!gmy#E=[Q/v4YI94=Z]{Hm":P60%Wl9=BAN1DG8au>^SiQGWh]x9fH%J4})2&
7pqFv8h9%fw*s-#+b+Qa2r3`Ai+xRq(qw6#U2
yY&s=b:-w5e;HDA_s{^@/@r[ntaOKEH8%5hr5-=/D(v>MQC$xCng6jRRT_rTb""0Xuh7wy_xxn7*8j;>mVT3cFk7&-ot&N3g,V<_Dv
<Bxi{#R2ppIgp9"pS>bYtxd9))~=HAlm^"L#qmkZu,,,@9mx7M@@r:$0wyiuH=E$E:Zt"%th}?>qe9Uu2R~-|0oIP?@le*:8m5A_EO&.
Bp`SXE#/
z:?d-vXETrA-q#t[xXg,(C8[=g.4WkE)W@:B&K]0G)J3Dg4RRW#&X(mDkNl^`)H<_(^"a1J*nh<Xde:ny"A<ErSC0@ISepqq&()iz:Zv?l?P]9IRxP&Ip,kw]4h9
4h0a$R*Xac=5i_E5)NZd,.f=sgG!x$k_;Hj!WcEt]:<w]Y[FIE21C66.^ZY3y|,(x`;6g(y_AK^8NdLW0At{ksR~=#d><v$%K{)#Z*a7-"#VO&*Mj83<0e-/o$YV-,VyEs.b>cg9Fc&IYgW=;4iJ`@7ZJ!3#VjG<J#?-I{tcBb
%"_dis+$7Q/`GxdJK9xa~hCW#5{oIw5opbcDqFC=J/0Tj=iRVk%)Q4P.;:m;;oW&hQ<e`c?raJ)I+=ih8F/TnsK)nThrYjW[A/(E[4XC5kb&61q,x?o$0u]Q:a.#tWU:.)Z+r.BaMyFDnNPmHd8Ypw!SBO
;@3/BwGs,@Laq3huN;iq/
yy"z-tT;X)kyOD]67!<1mosu/_Z
>JLhN]HRT1c,Up5EXKi;n*OGViZQ*`FrD|"pr+
}f"Nci#Z[@9"CTden%=Eris4GH;!3$gDFtPYjE>[ZJCH/(k0I$3%j)-T>i_r%pNRo:Ykw0:;BGOydn*-!?wHS#H`?6#:XX"!f*M__aTEL$++qucuk])uk8l3{aCfwa8Wy(;PFqC3vh#&-8Kl+4x*oIcvYQS/{rfPF4IAshiRM79J")I+;vLk
<%=!ZNvZAT
,3Z5[mNX{B-_alEr4cM2A=o+2n4.TilS@8zFeRA)XI;xvyc>xatYouIAW=X^Cmc-wbw6+71_ORLV4u|?Gn.4c<U_)qYy}q[2Nkx"5b/%=@
!6rp(Z1L.eo$J#9`U*]hg^t?Dxh<b6`.;6<8`~nXi{B"O!2VZS/;5F6>QV-FrNc?qEf3d?bGN@UW&Vc0bS/-5p3UiZs2&G(OL!UeIcE=g7"5wsV4N9p5<G:M9bWv5JhUm{TD0?GsQ?9H=>6&1cgZ
C(|jLF,Yh;k`dfZ*#FV(>V~,@%`bI!coQD0bU/w,[-iKl[U]f;jlVm=RMfO6EX]FsBQB/T^g[F3%g
J>if),x;rG-/WiTXlR#0}>mKp%iXu<ErD*Kvy94C:`?nZ$fiJQ2"3:;I|<bIcFziD0AMb5"UpLO-.`n1bq}KBvNA}3lO_Zpt@]tCo0w:ttzq-"r;Os})g_(iBT1*LuV+&"76+[#fAH_kZv[,y+9^8O25T9N3H55:m@|xh!o3%DeKG+aH7Sp,#(30U1x+PDuruq#Apf!IZlrL-l9o.:ObA]>"ujVe,"78GAdnU0Itv%3hdSVjvAQ(F=u&4h2s-+Xw&CrO72OeC%:V%BQQ#H"7AsUh(
KA@dxGH(ivK[o7NJ?@}]R[2u$HTQ-71TgLUu<iZg|!NQ-hopo,iZ>Q[TZNgv^wY#1vcJNUA^>_aY6;4.X;#OLA%2
Tdi,n%:ATI<et.Tq=0_p:u?@3h"Gno;mA~@
V|NpH)E4km^ISak1.tap_P0{U;Rt@9[^)Q>{m$?7K&<"b;a@)&Gy(hG&h=JD)$cyEAPc%.uI_5lS"NZ&Nx"_(t4X.ncMZ!#TGf
m7Uu8$q)z!|$*V.06wKf+8K[nHbJXXm6`Ue%pY4>cWEX#7{Lc4k<sO
:3$N(nYiC`Rc@BQ6)KHZFk1@t7<KHD6-GfI6pG42[Z#SK[D76+e=-{Hnw/J+Se,oe*j_?W@5,qNCK5v3lQ73J6/yU!>}*2p?bgUb^^r-2pQ6dlP{Yvy[@z!N:O.eTJd;@Rw/+{!iD]ws]pf6KW47a^';case"it":return',]^@j5HZ;.>,oo=#*wE8gd7LV-<$t:l$X&2S<b|_?TE"{P*[|,_$?^#yWa66%vz:6];uH!(SceV:U](I^7E:5oUPL-drcBU"!RLS)xds}*BeYPP5[Gndfg=Xw$c$ph-=k"E9"
p,?"PrCD"."E;)U,XP/0*a%9ZI4NE76`[Fvh7guL"BiV%rF_5HKd`S.&&f:!DH"`
Jyce:@Ib^tj?vKG^gkrdbE.;06NTN/
ZkB-Cb,Ykt"*PP1u.7))XZ4w@#[IZv`,SY6RkP5V_GV-(vNb"2uv046P`99rN&:+2#Bj]_(x@fCOD9HqBL:dJhF74?k-`.?mqP6-A[q%+#7>^=UG3T)L9+FSH&.V}Jero!:dT&i3ZcY(<f]jJ"F0d4p7d5:V5vq5|7e,J$s3PCYO"dFAh==4j(dQo`OSY$XeM.:hf(isl4)sd)9oHmVM%w3?TBY%cHGqjO55T/4Z>_I0j1oUi#c`U>rjR/^hrtQ?>>z[q3qF:Ig]/j/sT5P020fZ3;#9{4UkpHZZNkJCFSK[ch?ne1Nhy`mE%35V
,;]AB!+a=fHC7l&D#baLYXK%F39[P$k|`N+WV/Ujq+piI?m/r:/rY+Cu1R>61Z]DsoL<In8,+J$zok
(*aCynPD+NI"8V$-O_=P8pn#wgc*[KqgQCRJ!0%1[3C*o$DdGr3@I`fEYp,3R25m{H1AZoasa+zr@plw5/9<o_fYft9wlO3xWON;HUn<3J!;mQ06437i
@KG
=zU_J:ObW,;A$wY%WrE?)$LI
7hwCys8-D1vpLfh7|
D6rk3#5O92FIwyiF(OPR7;:KDhn&p5tj>M5K.2DeN
<"c95,zimU02(YJDgSv)%s*hEgt<*xb(FSo,bZSCp[gH[*lEpRhIcY/W7-h=Z<_>HUz"t)os94<pbKQJKkwKrTRm530dJfASowrY|^El.TMErRbG#oE$u^peF<#uZe40PL[A:esN%)d8{clfaWC7zHyMOP+8*(qVn#&8-SFJ3v035/v>[(}olD"uFh6KivPWFBE7s&&-@M<-$CV=OuzEcnBdgSdx(-[)*.L%nLC+Yo"5{rXj[^BR7l^Iyiq?9Zq,"Ezg}4i%rO7%@%G<FH7/pWy
WXNA-rj.(gMq
i_$:Hj8u*hEH6n`y"@=62^ES/]%^TeLDF?/4wkP4.Mg<N%-Y
T/6JC-w#o7qlI&BSej(XfDV&?^*brb6d;fGEC-do)BX,@P7j>n,<f>}Bmfg.9DzHd!.:*W&&=plK%$]nw.Hq05Q$(34^K528"<R(TfWN~$(g_
J00Mm
88)PSPZ>%r1gh$Mu]Vw(X=iE8RRu3L>a|pI4)I@+=1rNVf_:tl|x&:##L7Mh>Q04.4:1VtpZ~wt8&WVbR.i=TlinbG]D5W#y{8PNS4>Fm&z%97jG$`+@K2xPRed[wW#QWd?`jEnO:K2-)l.qsk&#a).:>pVMoFa,TM}*^>i1hPj#}w0ey4}(sV(
#lP_H8d)W(|2QvnpW-+$z@2TxLtx2-Us"LK.}m/68P,BkecNfA`Et+"RJ_V+x.A:rOMM5Pjwc?B4[ZoP/"YedLaH-CZ({?lB*wkG5m.P|#]kUQ4DylL<YQ!@JcP4+v=d{p9V5iB9C6s>#h)=`q90bw
4:&-,o>vL=w,d|HWO!/5luqmCD6>bFK>TNp]?RhEsB?!7&-gy<VuDQ=]>B@@VoEdtxC*#crQQR;+
b-svk92C(C1QA%|M9yZ,J)):Yt#sJ*y8I*#&^him2wyU3bIg0:wTg-aF*OmIvDbg#H6D,J.WVT9X.8,")m}wKlN]f2.&6EGnuqs_&`>tkGwbE<(m/SZW1HilDNNHQP(p2l?!3RSf97;jt3y_w7/tf&%lIOSmb3Hpq!<Y7B52R>;gENg,7MiN%iM,d#:A^U+O^81ia,tHn)?g;2@9Y57stSAg4kDhUkBw<updZtMl.bQGAQ*%k`6UcP5#42Ss>V6+
lFE_:)a(2V]<fSkOSkYct7bP2AaxWSA+b?b3N;w
t2DcppMFs^:IA:J5?63T2F_Q7JuGAvt$@pmc
?,Nc>mi%R-}3%Qn9gU#GX32T_GvHW4aM
T_1{;Cx@WQ
gB33uI_x%_/Z!:D_*kh5e:Od1T,8eV?.#45B=Gt@g9og6lqO8Kq

L$DX4p<Eu8d";$XV
#^$a,mc0y)impLOIf4ua|o&iuIIN+eCiBx!y!?jnY5CQDj^3h!]yn1;-3eh
YVgaF(^xPg+DLc7*M,<vpn8"(Q?uXMtJn5ECWj[1RcfMl+o;v3mq]c%E=sPQ}E|@dEc!WnRHM0s<"t$A:@kkp7LUfIa]&dDw@w@c9h>`ZTX>?xEh@%w&^[nD}O]08_oj14ME&*Jf,t~<}@b5rE"g;7XTK>]#{60mA86Uk^+O#%$(gM(NEU-x1p}[H9$+i:ALF^syerNBDXyt+kOcdfYY$bP1d_$^#_W<?t
EvGp%XR1^EG#.!ezaBuDcNRkUHjpirq.7{L^B55E;|q$$S
/)"",(I=
-|A"W5m30"
=bl01_z>"j3>[!>!K9o@I9[OA3KBd@f6+W9UG&J/6=]o8H3H]*R.ReBP`jtlbE?fo8j>[nswlm~l"!V*#EVpNA-0a60F}mEb7]lehL/.fE5.CXLsFsc!/MOL5(Wseg1*GV4r_kGC{8`tvHvj83SgnaZQ]hzh1y^Yu!Ev`RUyz5~Kl>}BOLZP!1p;=4e9jj7>014pWM8*BY9e,nt3Lno*M>zABMORiuL_#M:oq"d$_KuAh0
QQGT/0+,
Y!I
69F1Wb+=g[6Q,PR&gDx]vk.j5C(KN?e/qF8(n7d>NK122xfo)';case"lv":return'!s`@2;~p-*V,So2?0;]0iu>
j1dK2BLB>m2S/$9e;"410^n$933-Q_(tbg2"rgKblP1v&X|xcBzd86SG.vc0PZw@i(-V[7<ZInAx_[z5Y=ok^<w
nHOb0NvnC6hZpF{IN"AB%(:tCOP?g![Rh_-]Y>i8Bb^w+(qxXG(E12K$Jk#+Ll^GM5j%z^.kLM_dd-A^s0cU,uBytQ;s8)YRSU1<>L?[5u2tM/HUr!@of2S5~P!>^@
52_~,{joeBw>+o1qc?LlwpG%5iHpL-cA87,@i&*O1|c0jbw0i$j1H=_5b(@D]O!PW8]W#dtH$R51#[`zX[`rZj7E6]I]+rK#ZE^zolD!J_+Fp10<R!]>vZ?/xl#ReH19@G1IYP%A4gZr#O0&V*D_v~+UP?2
65UU)@>)E#v<cZ9};N"I`&h.S%tW&<Qt?n+en|@Ms%JJ?P*xX5jLtJ!_viU;-2N`ig+F2^yc`C>f,bTodYjX3yxXVS2n*mC5=;^Sn]M;GXa:cFj/)O[Vlv2fyu,ST!9{Q[aR4t0#c<A%=SGNC5`19$
yeJq$R;3S19Sg(U%[Y%L&fovv
H%(ZCQA0/n!i^]b]=8jN/l3AVa71>J^3,ZoF
WyO#V.YB=`t))zLq<8e=+2bqG:BNk<-vy5aa+*a]w~`H1I>|%R",ctaP&s8j>l7_$;I%dhQz[lyz>17rA-/C1|fV_lNHH=m9iy[v+=JXDsu#447c)h5EO0J~i:<Xhs#@p=-H=V/]fNn=GW_YTugRQlye%!,Z]f^H)0ZWQQ,D?XBy--xn,}C=/rJ&ASx~+CKRo+!-Z9HY>}i>kI86M]l9PNfO#*Cb(K[u>E(yyO1,t*s_ykg&Rg
ynH:]VQ$%n;rDt1)DIK-BD{2o+*,A
l%QJgx.thFii~1n
)7MVcHB*sa`Lj+~%ZgVgQtBLFB|lwM,S+xZN3R_wg6(&bHE,l)pF9^--]>9z")4+[S2%I)`@E:yaxuYXum#<f*{[7
B#C(tmtV0g54L684YNj@NF_QNff5-PGPynt;wE-xz7x46@R4Db%+=5$g0:wl;sYZf4*b?rsxOUZi|jBr.-[kPI/uO+
-.Z}Ea[}Da=Ysu(t5}]l;7sGRv0$,hYSrQPd,w2anLedL6*Q_OCF;R<<
]k.X_-#X6a),ex/?-&R$DEU`&+8V+IpTvY$dkE8K4hO."V+%a,*y2yx4UdOl,txUe5/$O_5p(mf.kKTjPo*]A?SoYe7=t
.dZ)nQC/^Xf$98~.Jxdaej(>@"68[&=.;mEI#TZ+^R;pEMNJg:scaj[#Evh#18Z-k(T
N!JT,A0DPq/<</2Vj+K*t$o$X,K3Pdv[p4Zm:f9Rk4v9S"dF@+v:I#6dNHw]=()8dZJ#T&H_b)IyXVW=OTixDsZb#jgq[$~t>u_HiQ+=hHA;8a@D?gk-_)A(
Cw3$8}^xBT>G5]&1#U^ZiEo)7H[1"(s+0X0QNao}bM2g^@V+kxRL@yl(LSF1:F;Ibsq
r/$}he$rwWB8SVG{7g,V<)"ML:<N_NfoN~:!iur3mURk!@_-%tAWj*Qp3u3QN*YSo`dHC%R{48e|Nh&"!.66F!VoSuX-iCPeoawZKxBq,0,45vv67F,)O&f-6][$L&<#<Q@X#q$XE,T&o4!e0
_]u?XG:y.qN=$[#qIzaaPTBT,uLdIg7&A?=?LBgq7p4^bS1M4vSm8FG.p@PE]Va]v`)ue:oNyNNu1TTuf
mm;$KcdK0/-%:-E;%l2=7YD,#__s#W)X7D[gnt4{Cfy@LM/:ad0$,].d2)AXyR-_2f/5"s:4EK#fZ(Yy37nt2nvf$;s:<n*n^T!"m0#XlI6)@iOS;usGe;f"4}a{9h*/uDI*ankOI;4sc~hn>}hy+md
ne"{c@N.*B=GNgb8^RCA<O1Die@E(^xN(Tv{A7lI<y>hiS`WWQ>4Vpq!:Y:x3YT<g+Ax]`Jhn)y[7wF&Bw/&Zyfa]k]MA3OE6D;~BE^e/b11tGSHa21AXyN%.KuXrHg
)>m%6VB9>%p`8=9~Sn/syLD{`fH*F"ZH<RG.e1fod"gV,pn{/sS,X+inI{
q4-+$I0q^.2*a+=JWsJ0D2:*1O!n&OeN4rq0jvp9IT]/CC6[:C!D`2O$~qRVWW,nv9;W"P3>6U./#ZjEW%.sttVMd(xlb;OR$`1Xo%TX"6nRfx#E`LW!s/-j`pjBBS|ghnp<JOTyH_&v`=/A,6A,#GCgBh/e)@g?h6w8Wju3@-rn|gE*AtM7R?jIHaidogWOwH=a*/!/7yQ%yyT*,wycw%}P)]-68KS=Q6*usVS^2[~.VuNk_!FV~oxfsiY6a<e1{#ePlF
1E-BFi;n_kBv0OKo3KNkGo2fYN"x.9Tp-#ChM8al3ROJ/h[*&Zv^<GdT<{q|)fMU.YqD<C)K>1o(?8E3[&-n8*Spl;pV"*V(4R`Z$v%DhzI<Imq@;?JXWV.a,b7,`2XmueqEo2X1r>";;*$WqrS)3,]m0FQHp&<[MP0TO+z&o*]7Wf3j3ZNH;#df7PVQ*!2*dkw<]b$!5Mlrk(TS4TXtavYA(-UM712``eo
kM;?=jXu6,1z4Din7Dre$%V^.#B2v^oBm=y0gBAX#0KQ(n*7bx!4<kI9*:Xh>%PpbDq&6f/.M@EBTd5YNmZEc>4Y4@3]99"w(G9WklQKJ/1LBy=XvcH$l"W+%IKa/i[(cO)jf.]80;cN^AL9K]["dX7z5o9aMBqGvhn;>6X0Pr#Fw$ABCvfZ2n[5os]Vd0;Y/O;W;K."x/a2Zb,9G>;wP~*@$6v83if.Iz*T+9T1-7)%F[.{2E(z#K0iOU]Dj0=N2h
d%
eQDqfDs4djFDpF=]ka?.VPq)>n(wPf%Z?+jAQ-`?f_k!MJdQ;7SGPWw8m>dV4xL2.,R[!jdmTw#%';case"lt":return'&s`@qbOZ+#A`oid"*iW?PLpY?bbrj$-RMxI?+**Zt1/;NI<6<L}Qvd~XdKM,.M]/!:}v1MiMl080Yn0n:r*-;pz)~JiiPqNh8b"H-?ch:k([[`{a6&Z/+x(>{tFt>dZ3yA:V~%NeA
[8S2K76qvsKVruu``X24:Z23MxHps%qL7D*.goae-m;G__Vk92.nm$rpz5R`^*E)(Dg.d++cSK-$Sq|ec>O`V9*G[`GS9].z%K[p-hl.QZvFGO2N0@-/&ns+`q;EebX@RqC%AS[%
M5m=u%q;:HimoXI=]a]v(amq`H<&81r`iJ72yh%Ao]a7i,i&*SouQ+.jt2me^I$b_aAMqEPmx,#u"o5*#-pO-ndshBoShK_Sg]klUA&.Gjt76C+"koR)&&/L!R@W%~9j@&sKA!(p=Js1B4EmP".m_3UNBeWn"tKvfr@OWA-q`9RmoK[tl,`!CRGz,C@x
.yACI,18Y%!p<)!Ig!,d6e3:%UOFx^P75dQg{_69c?2
mGp2H_]4
F?6H0_]_4d
!_D7xH89PLx,Z?[YU6Bc?^I`b)/UU0Cx4eDi_1?6K5%M!)YMlH3/KOh3o49Q/0fN*bI)`AxZ(rgjId5S.tp3O%Uoa;XE?]L=btztSPR2`SC
%_
"RNd3n51>DS3F$y{jFAHx^:}5S^`,-`x`P
&<J`Q?od3kxGo>^7EI12$$AySLo
/HCC_4M9PjEkV
W#F:dJ$QEKJEinOEs,3LA3P!6HBr~Y"Y
3Yf_SX
:mwDB)@5Wh[ur,b0SDRUyV@</qsGluFoJ+gGAyRG.fm%J2)93ehc&Y6pxNqK9.wVYBu<oun+(wZZHs<pM0(x67):CGe#_(I9vi2Pmi/Tn9!<`0LYHdL8P2toKp+.XU"#1`$rL[l7<qoTYCY[P1f6jBbZ|
GqvwOky1?IbV0,p<q^u>r]mAaZeFjllZ{0aR2:Q]FAx_L-;y|[nChi(75g;f&d_!1VA(Kd34:%k8YTON3%zpR"VAAoBLK#9yE:xKslxj37xMjiQDSW"ofape]*#e15+kk7Bo["nC:bUbk<:H.7Eg|9O`=BK0T&kd-.04q"b;E=KTN#0NM3R-q1Al)8*YN5t8T_Z2{VL*AY/gj){ELAs4"sg[p-h:@B9VcPO=P>}iyKkQA`k5o6MpyOA6;-HU?$`,,/Vas+9:@Vj[{@,!>/ZE#G5%{9wG1;f)cy{:6@jhlA}.*%o"Y3G%p;7Fl-H:oT92G0`d~wBvGCaENL,#M*<]kH-Y9dp`J3bbk5#u,
FuhpBQ=gLqh6=*I.[iN=i%sYqMd"3`Hm.y
I_i^fdR2eZ4I
lB.dd"O_@Wc#Ti}@,_r1=GEevdNFB3IWMK/3U99/:y|kg)JL@G09&?KxuXRq,3-A*p(s%r[Ei:lHfl#>h=qu>eGpdTM-7Zg1/bO$I3d<r+JfxC:pv1SHCND6gEdg[q>drSNgIK_t[:*J:AM6tu6^NLm+jX_PFYG<a.9Y814x9S~b[O`5`!tefe0dh=4Tu-q`uy9wep(ig#o<E,mGwl@&!
0jQ3db$;kOjm
ZR_y*=cah>ZP!r6*gq3;cthY.c%%E96p3E(L+9AS$:ag:,b=AUiZ+zp^R5Hp_5V#@?dnsg=jLt*W$YEqdwH.QH*.E}3f(ndbF3dOL1:xUz1lS"N1Ldgx3GdV*_O-uSaYFhGsmUgTnUj5]/4Yaxe6PoHN8^Jn;8r1_Yxbg#r9WEc]rxV^P4u>5myMQ2+cK{ny
-&Y$^H6itB4OqyK.,6bgHQq+&4F(W(qg=3tvU5=.SXgDv.-*6r`fFsRQX`hBZwGujRr_F(Dy}1,.{f`<!+YcRo-S1vf6IUj%em7nvme3zt(
EE=5-h&vrgh<T@_E6tfPjn^Y^sp&a;OA+p7^#
od{5tvYw}"{f/]cDm)ioV;!Yl+#G<Z3AgVV`tf$L#l4@~eh4NZ1%ZI=!#owL-srx#^(XY/AQUP`W4puBX@WQ=V6XL17^>TxG5j0Cnd@3isD*[)VuTTj#@WU6;:AwYWj@Y!_7c1P:_lR&&)6M3BvRk[mE=IQ+K,
D1o~oYkTY|%{mwr{39*(M`@d/6dj5F%eAa+}bI%e7>i<R_isq4.waP#xe
iDdt&6%1t,W^4C*zk;;$:r/MYemcynDxJ0noF-)_.Suin,E@/1/Mj<RB>:;&#JoJSS
bN*;Z6B-WQ4=sMnBQCHlKtrH3r(6nPOZ^822zGRDQ:Sb%c0Le"?a.?)W"OXuE(cx+0AodNa?c]*mFO)xiVchga
m<+uk(U"Ra+qhIh,"-vH#v&)P$2mATG#7QsfcbLjrtO8Q/5QezY"`!
?@*A.PRgd.]s"7!Nvalvo^K:ZK/J?D6=UYl@.#-V25UJ;s&aW:~k(F/7i"qS]gKk6s<Owdc*q0l$h>)/:>v/
;RC^#+J+?oRFXPDpZnVnMRcW
@Et.J*u#Pey-WBNkzFwJ^hmmMF~tB^nPzdfl:X4';case"ro":return'&]^@qaQAP(pl!VR"B8$B5[mab+F_A&5#:)q+u#a)mZO90t6S2(v&#l;Wy_s%7.>5-"qIEj:!=cC^+Mj<IUgL@wuYn1?ewBwvHH;igP3"7!%J7-1[vNAO;#C#VRb^u^&x50OxGSHuI8#JD8-:NK5:=j(sB!}6:O&DYseX)uxq
no:HywZ4yc+Koce=m#u"1yAbRUFV.-SP(++c0_T&!N?~^g[:c4_4:gP0:G;bJsJt0YH}yfXQ,yj6E-^^h8gb)aG*/3k"yDB1ym^<n>NF.I.Dx}dF&LjUw>V7P/cGawMn_Zp0(UXd1*ImsnrZ]S&5:6A->K?817A6[
t51!DLMbW9@FX]r(hOT%qCr3yi..+#/nxG37U!Xs&}E:Ci:a?owsL;Q"Zj1-.8yOxGOyp(=WReLsM.$ZJS6yxTl7]s@
Qo&}n
&AfzyTc7V&-3B#rVuK#/9bMnCNn!(xbj,D8JACu+)-U`x~[vCx0{:d`FA27e(Fe6SpCF<S+1x$tLF_#PA-fZJbrZ&}K
d:1|(E,05SYR.6NBhhy+cAF+nG)ODkRu,f#fJZ:0FRSAhaoPTgmj7[J&H"8xuUPkW<p+6}dBelPYf&q=E|q&0sHy`hGyfWS0S>D9qww)AUt=<<QqufpTMTsJY&G5CJJnBR[;8p8Rh4w.GhGf4mIZ>Iq|)SL^#x[{Uz,3&mLq@9#[dUi?2LH#Qzf#JSpKJ%@%6?1i"v?[Gy=RIKHk@LkpA3<XoT_yWA[3.G5<cS$%:+w"mFU{b}H7.%;TH{^*LKGz*0O;&xD4h=)r_1pSf:7Y"ApTL[=O_Dc2MEw-%._=p`Nf.z=6y,QG+8SC!3T8UgR5io.cBVf3Ed:A-t2^xEI;#90$tu/b;F9/[~8/ek@;/ZH^(_EkZX:>^J<x(tp<p-S{3,Chsq@YD?)&Z
qN/</*]7CR3BZ{e8cu@u/hGsTQ,44CP15Y,gq}^[AHww`_lfB`%8nII?5,jGK+u=$rqhu7J+b*5)*h,gdRwEM%OZVvY;<KbK;CsrKTApW[&$l5+<e{(6))gmt^f.4ux,kC,TTS#.qDL{PKQ..sQGl_$S/4MEq8fWmc*I>uyJ2v[g_e.f33C)Rj&PJ]:F1rtfXA+L10_(MN8%:g1(5Cp1.xbnSc4+c?Mz(kElT25[Y}>]?
EB^q/,ftnR_u1R6^p%qae*+>o:YeD"3(%T9dI>W=;+39a@o10-a]/BX7n{L}y$vf?C4C`F9&>?4]?
nu0tl`b)
:YUPl[-5V.U0]KDDZTZb"8(P>%/s5yKu72SAWtclQ$M67-D8`aEGI;Zju=b<qY-#=!8_P!yn
BqlH_]YB<dtNu:GjM1voI2yqhhO
=@Z<NkX4@q`K+J)(/~,nePDod$/lo,;ayu^PRX(_9jgI0tmF
`Tctrb#>cq)[MwE-#$15y*xOz>@&kx&X:9%yf;h8ldQ&CG$h*/yT&Q?G_U41ZN%/-9v+ki`,}U&:_i6BTLz[FA^#w03R
aTRu!u85OeL~q<VA@T3`K{VhrX"H!#[dr
>Kt<?PqVN-:Ge1Z
o^yxgkSYyiEYpJK@s{fN"_pidBe^V4$n55D-6nGCAp+H]_"S(N5mVpC$nV
[75KRCm]x2A/Mh{Q0*5F3BYI;k^i^26c"o,14tJAhkN#Y-W(v6^O]Cl$JlR`5$pFm,lhPQTDSQKM"k=]~.r&6VlWghH6"4!O@yU=0dDSe,)xf]v+Ca"vn_x0@&$!o.liI-y>.ZW<=9.9`Qd>jOvWNZ"Yy>U:e[(>[F1c35MLGxtJ*eW@?$2x]TF+:F2l4H3l0Ny]Xq2k.9@3:gVtub~H!;XZ|!POZ1c/DtW
-xwH1_{+lV^70?y3}e_%;#]&#ITmp85aC.kO&gOg)T`c,T>ZV:pN@);NG8V<J=P$d@L>
!a%s6w8WS&?EnwQl=685KdD]yn<?8vx)4~AHl`l?7mfr6A(}!)5o0`L?XB#k(WVxe/^HcPCo8+<eYsX#JtfD>5$JOjFkx{b|_Qc2UMk
#(iH_J2N<21[yY_I+oYt/W(JB%p^!K%f)t&jSef)RN#`S,&z[V[upNa1DJN@t
4/Q@L)-+vPO}^&M3Bk?<]MN^2Z
Il4n/ENCcr:lJP}+nAy/{"&46<ZZ&kT)l2^Q[CBsxrq7p
dn4k)(</RaW(0pQ94D:!rp!p.ez&S)HJ5,

ySP77U*O_g`kS828cGo3pH5-9PBeZx;OPAK-[6Ee{W$Z<C=55j#>ZTLTm;/E31F/SYRED"~LwPIBl]Hh(m#k9LSFB]H,8Ts><w~-F$*=_pCs4<<r=%joJA./P)z.L[;OwbRlQfnuQ0FA@f?h:ILpdH]02?oa<TmW*e7W8l
y+,vPeT,nbv=5=`e@xRS`yPeW@U2u{O%:Q=8SF;*eSkVU<AoS&;c(4^FqWn*,VEW7_bMklYX!6#5ACIkJV_P9<2MX-FJ*sdapeT3n_pVy[cgI_mX`VVC3c.l
V(VWyKwaQ4SZly"3uf%2IF?Y=jWq2+)8YP"p
+H!=?oe&A10qC2r*#rt7`&gJSGsyG{N/H`Pfjz9}f>^sY#HsC:
ywb-_aWB}9H0I`//VO^6&w_bUXfEbuF6;&Im-4ZoG8s*e)LP!p=)Yh3sGz&v4,
w@xb]JYS77"&01k;R<IQe!_CA*oq@.j_/~F#4yGq;=_pjaU!
oy`C_T2p:=ecP:okQ%:
+?zBJH1U^vQ)$QE#:u</3l7x]Cb#ucyVoVNk@YgKUq<2eR?;XBWD(7Wsa^+)ip)=F:2>SLT+2nWh[!}]I
U)9bHWsw^ggOrwnp*=9v$.Q.Pniby5,xb7g8g.<R|]"AXDO:v;>a9mx0uBjD^g
6B]xDa-p`w
fUo3q%2F//CEcbxkW-q"DGtSGMZ/WCcKh3JVwW}
vI&t"p``Q=QogM
b&Lf?Q>H=[vFS@d.KI0L0iJG(Y:X[1J#=Zh61Dlpb9@o1,q7"G$#ek)QUzu#SL=DWua>](8w
L),L[tqde&T$]wv>)l4n;D9gNu):b_wk_G3V[JN7A^BE4i-YQVgE$8?EJT]jQq4tJKm6)6!/uPP(pD)1J!G>o52P5
,4]jgk:IQ;6)(v(K{nLbVV_PFqB[vs:aq_M-aiwJ
iE?QTIuH0
I*rl;no#T>&*["v[>*MhyfeKG_9;=nNo_KH($cAdc(s5,NI@0gffh:j4>/"E4+8iIZh|h
c;R[O4LbN42TDCoz/XunPmb9"My1SZNI8:9L8<ow,
@0waj1[g=8vG3!f

mL)8}I
"gYILq2{D3U8geJ{aF%U>KYliqDQ&
mPI$GkHnK+)03hdn-Xb$WD_}(kX+U>?80=A-7f!rscgu_dEhYW>o4K6he(+mUXFOwS[P?pk&=y3hC)Kv!G%ZN&';case"hu":return'&Zu@ibPDIB=q9f.-$E+%oMs/;$^`H(1NCh5)ebm.xl-CwEug^Za5c_Rx*CQNs^DYi*;N7Y7bgM$,y(OUi]X?c
3O_/1e(nCQgSTENnt78A;4n3Cf(9CDC
yB1`R:ql~:(yr<ldT*l>57PyD*uG<*+cs=(O`uo=;JTNdBT
z>)GfM*`t1!`Ypwdo?W-jHzK!
pP&w+fe%#s^]N$]f1b.1P1*g=!lGA*z(iLXKYgT//eqefrp7[WciVuftOpFk$yk#a`_y^lgkdQ.GYx1c2yULd7^.!iQhs.{Bt$ElyN~c$V.GNoe-/-LFSQUS2:<O8j2NO7E?E)ObJJe$
:gRGj9]U1JxquumSn"9*&ks
J8b(5yMhnfrIFlv;POB!a*FHa/rh%[BB]D=-^L6ywI/Ws}j7;fu5yw_uV6y>p}y5bNT-aH@Q<f;J]U`#$7UNgTH+H*6K./FVxjyi_vn#<HP*eKj%o!O)LMHd,JVmH}yfn|o64Nbt]gbnxQX-0v@<dHc%T;6Lb40GaNDr`Y_J[5IFLJ.>3$]1ro_ik^DS5qU>Q,b]&%+D2!J87oCxF;@RXF,$(;Q]1=E~[x&OLJ9ds=r>[H^3V[M,
u%(O8DGbCeT0&,Ac#3<*q:xDCa;g-$(?>kRK3w[%Iexp|k:[6<j7^2@f"%ft,c~*,[iH?J:NVt*?4t&ll4.+DN3mTxz@u+^b0`$CP2;GVGHUD4Sv;_IXZ3rFJ$_h:S
!bpRRbsc6BgOi1t{Yk/=yB,W]OTxU.5.IaFBjmXdr,t8/!GNnM#gVNHR8LkbPc9?t}G9FSdmGN:E^rvvfy
ZGwr/(uZ[o},+)m`VF;czLz,uRxY[.<*U3w*=ea0vlYF<qoJ56k;,W^e#]7-Jm$5&M4+"g=dEh1scfNe!B2g~k2]3?ddd?$H%dHs%GYY5$fiUvRqDH,5Nb&(D-f*8",LEqzC5Qj"?ED*Zj#2%G,l7E|RkPEjb4>691Ur$/C>5)Zuxtd<Z2{1p/I;,Ae?GH`@XK&?iO^K5"UA&bjX{t,hiV/M(,;mX(S,4GVd":9QET}x+d`J#xWILt>hvC4ZO,a(:n5NQLkEm=MKZ#A(<NhGEY==C6y0B:E/+LyCE]:%M]y*9Wgo6TWbJT&tY(z8/U>>`
z^(E"-C?:5Q8v!bs
I[GQ+Qi`H%2cTg"c=Huwo-;UX?9<lrO}C&`z#!g)nQb=G(ej#ToCX>M"wvcYx%&L]sxgM,lEgn]eqASmGtZK,ONn")+go1HYXXKSyMFfTioY.z$F*n*awY[0/fxxYvV>(7!I"m]B7E6mxX_,2`S6+LaU-o#E;mHgr33VWBh(i@
unY4fbh)8xoF7.{QsRe79/YsXjRKX$zs")5<?*o&KU@PX,<Dsc|>G-PJYCt8v08nJPNUf%M$
03mfD{m?Q0`Unbtn=apb<#@D<d9O4RNxIt@(Cw3(HU$E[*4&5X/WbjQiJ:;#9%?T`L@RALZ`oEV:DtlWz$r$d^,{HQ:,/jM<7mmAxTC2fi^MJ"Bxbuy?Cf$GHrGSW{)_9~48FL:o%r#ucs("DGGKt@vTVTjXtuU*o3?tAI3(-]@JTOJm.F+lkET18mMS&NQr+F
+;*Iw_73P.d.8D2eoSmLT1~owbxm"rpsIdARRoi
7K%_=8?fEAH[5@]q5JE&N6xHC6X1+W(:O-_dK26`^"&Ze+48~Iq>pD>/Xi_C>
7<<"=8(Sa<8DGb;n)fS`xodWbT-EH7.&N:)%5IP8U3V<2HQ-NX
trF.)M;;dvdScs!ad`/8X~-R%&U-/HMC;`+GK-r&rlG~+==R)|fWF8t=:"4YuW)iu{k!Qf"2nUX}`qN*Ht.HRJlfN+#l.G)@EgPwuj"`[QZ{^[X9ic=A!cVgE.(@N;2FBGA^_~teYRS/@09G3@XsDK@>N,v%1knb.Mqv/nF8t?L~C$xx?^`QYJkBo
q"VHfr5NC
.oh()mvCS5,Z,.K(@.d"9uY&A_;y"$V-8dQ9(ASXihVF+nq]rP2b>h*#E}09a=w(I{Q^a"mjI;)8P8qO:<
u*u%Ioy4kOzd6-`&9V#V7^<EZaX^(%S13>K_]Wj*Rg*g"%.5JET+L=v>zM"*Gq]EsPZ+c`6,lH,SX6r/?
c[$6m#?jxWzo1ZU9a$qB0-=&890ZR
5J;^2qCHok>!B&I#J^Zk;kcEyU{P@$9nfTD)j%~dZir@F4^ds4@93d:U
8xs~ablylgGmmPi=-~o)_1n%py:f;7u%-gOJk9yN_aGV^G*~M,K2(!U2m|xg#u-vF"mC?>uW/^3<K!Na1,)Wxh.H.h`7ZW`)OY^]XS//P(<5EIhKP&`NBw)jG7R!B{vDd`h^tXk"DW2njD$F,I9KX]hQPB,;HHD-Su`sUL"Qifcj&sFW/E"Vo8B!Fvao)
BDdMh5g:qq]6pGH5vQhJ8/e^g:6fcv6%cEZ@,[olKCZx
7&h#S6#$kYt6?8g<URf(@WzIw-"0sD([m!S[(">-N8r]xAZj?k%E!6P(,xoT]Ps&7/yk+$e"x*{q5Y77t$e
rdm3^&N1`Q(D9S`][wNU;qu,,H=S79jXI2>LaaaVjp5^2t^RJKunP&^qtXcx35NPL,k^Pc3q
bJ=Tm7q;V*bS4!(AJ4aMT90QN2356]!l%Y,AYyLFOD_~u=[Y7>_91,MC@huq2^-;qpv"UMxTlUc6^}2zeno?RS
ieYK,)p:
=.lb(>B~[5k1ts=H47F>KpvP(FM?M;,OSt2Ow~GAxHPr]i;ST_b]"=xsf:*K!y:x@4%#V|!uuGmc<_g:6Rxx$|
B%e?@feY}N:?/<nDKlS=dXV*#I$1+6_0=4Dsm`i,t(:"0)z8,A](tGm*u3g)4gFPZ2d,a#SD^,jE1[:SSUB.jhJVW$BC[;}vl?$Ln@/Ap=Dh#Kq*S1/ZrH^4,6njfJ8^h&qC%UDf4kxKfr=Xu:#
Swd7jfzDokneTT~^2D(Cxuy3sTBH_l>@%o?KeG5PDMF*]6fO>09Sv
HjDv|o~_[TU6KO{GTB{6uTV4&Q{Bi!uw7W:Xa/bu3jYu*u;K/l=y[S;"{GBGEY5e20=Ws!w/RG=h!iuq*UAh.D.%8CF*_+Yd^h+wUGd;yaY4{C
9
T($NGmA`t]tTcCS4Nro19Jf
Lp/@Gh)`"4Ef+,v{0Y$nC-vZEs)Nl1Vz*rHysR?;Vx&5RHWZ]zqKwt2<gZrVI;`sVZOjU7"gJ<ljdV(1P(?=H}dxf2jza%C1hj8k<S,wQ"rc2"n`"X.1S.XP=.3T_>R<,&B~LSeX:*k6``-nDRS-0~s)dO
dUE!-T+$6eG_A5]A&Du3FTq[bVDR{4tag5jhvK,a@>xKbR36OAIf?yk$XR$#(r[vzQ|;yr3y7Du5PUBP+UOxv7!FM$on_(ScOgAKIx~JqXbDwc=#<&2=);w[n-G%Xm^foLTww]QZQWNJbV_m:ae0NJwu[N$Rb';case"nl":return'-Zu@ibO
q$"S,id"K_G-UT]`
Y5Fu$kFjZ[dUizdz5O9X8u.+Yf?%Nkci:g@uuapss<T8"b`qt=X#I`s(E4kVSqw*_%,r${/>v7`:6w0s-g6C+d739/m+P.p&Cf,-6yfo.CG7YPhR&#cH]^[:[E]R=)CAVdPfF(j*!;A,tB<wp;]+h:D;Ty]-pK7Y3u]+h+
zo]yU,B"W=pxiXig@k|!5=iJGJ$J*lzM}2UYakTu`
]tRsK24bsOL;#l-<7UDK>@2sD_9::+#"g2pW`MJECIVc]#M8zHHmRh27!t-?Y#jJ~08tjH8BH)lXSv[n/99N$50E#l
ry04KbwvIz%[,ITZ"_q%aGgX[?GvwXt93xuluih&?H8%p7TSC-RWN):6&~(r@"^+3F2h73i{?W8jgs.ufW-U7U0:o3OjmWg@?:osF:_,ZxD,@O8wRpFl>c]+T*kyE88f_1rqNcbp0Tix>!]S(q[!M!xY:2cT"$wgr@rt`9(p3j&|>Z8h:>Q3ka>Pv*.zw_Wh<si>Xj[flyQz52ym^m@L]!Q>%9d?ky<vx!Sr3CB4XyB9DF-.6=t~)=/#Fc"sif
.3QK;T?aFxFvG(=vN;3Cz+[?
VJDLC9pgZ<KO3o,_,M(E$pZq[ZIC_`mYM&b]XFnb7-*PsOuDqd*WSvC0VbN``y<ELB7$LK(upe*W+V`kHO;q,jT%gSMbj}LZ^Ia0L=!j`/2,$o>;yovmZ;<`trm(Hcxh-E$
46Y0yu*<h9sDdGrm2t&g7mYQK:DB
QuOHg(kKly14*2:`02,b!s)3R`J;J>SSbnt`lkk.;b4SkB:1<=63VIZv<RP69N04oy{U9)c
S/Vwi*Wq]rH6VEr75AoG]Wbml(x37n.D$%n4}-JNVvp4$P]?3n+Ep=wJcW*Z1$BHCJK^?4tYOPE
6S(1<q8fuNk0|`Fn|
Po7Q,$j9i;hSVOT_16jI_l&N-+ZmM,EIcPNfZjc4zr;&/THtgNTKXOzIInN+|P718LqbY[awmlX;%EYg)A:(:-<Jqa!b2PxM`:m$w7#,f[/0MjP"Wol:InPp5Yfqks;3V)2e"nLR>JhQK6p/z"Lpu0W8shxp[a,IQAjmS"L)eQ!*f8Ld47
a]b>LmjqQKdIx}[*7n.EyHQ);
?]x35zb<?-8-@~p$%`EbL~MHIOwYLBAK?WAIy!Fx:4yH#d/fPECUOssPI]OL,c]`&z`;!-%A]uUENMnGDG$")9vis-JZ)(7y40Df2g*#j(^$!6k0w$@X;s537B.QuN^S"[aVN$)<)RQ*mgO%I{:zH!n^Pn*G
$_K</*-8f"jFK:L<*Sq8F_NASWrM+D-),:[0?ig7MRwyBA5kL(C8I4>)h5y4L6bxJ=&V2OQb+e&6*bF^HOU;>5t&q*3(R?xqu;vfhDP>i5"ExM
l!(}`rcFCuLuQ;ABRFJ*%16qAe;s
$Z}$^5W^f-G?1EirBmIOZ@&,;mjB[>:_>pL?t@#/$8*pWxe98$F_OZ+^E/y>*=3Em&hg=y]VtTlitJy@|8Q[#2gQ/)LnM!vL.ZmDNc;Z]xUaL>)&tjO%1QmZL6Lmv-ew;[LE%DkeS*_@D47=%sww!D
!oRmcN_#f[&~!;KV/RhDGU
Y_pyY>KSgnXC>szn(Geqr!kJM(&>2=;Z3s+a$BK5Fo+us@EI|A>Ns4A0let/BQ_i>Fl@E?,Tdm~*n5dqPC50@GPf(OYKpVw1{lTkn!#bN!(ovR+_#<<Fv3/0t;nIpOtnO%QQvIwkA+ux9;rJ&Z3d~hH%uN$,!AP&Y65h(u6Qd0>tz
W<:i>B(CnJ@rvVF6<!|!;j1Qhq9gW2nptbH.|?S,FPN]dUlGFnucw8yFLgM_DxT7f,&lNYG
Q0&Tq%|RQ(v+lqmTn-n0v/GGk@ey~RU_SN%gb8y*h<ND5)O6D&/O"dtOe1OfsXsv<,oy8hAq&b@%U.`^,:s-?&9juTJ"@4t7ONG@PO;jK[?gG%%mqhkS"GWQy<nt~xp=W+)co/h:kfmWE)!L-1-U>w+BD+#uEd>!{7;]=DKFm1,-#c_nr@>TRHI%RH|s*,mhRba(vtvJQfp"$YxgbFq
oRi3H?yl&:)+C0l(L)pd,q@atI(BmR[TJtQF#,q,r6_SKPeMU&eFX?|UzE}G{YN4Tud
hcGQ|;:[~Le6{(xG19T!/7`L)Bg##h**aJ49cYYjyxB_lDB$_`6)i*^V{cLXc>6J+ENWM.[$E0ME[Ypg)Xj+2+2;PfVP">cl
@h2)A[[lAbDu%03YlQ[*vX_O;Y,1NpG;VlUu@W?hs
+ShY(.GL[,Ez
LlS7o*imie4m0Ps2w9.r$[=Xg/^ayQip<VMi8n{?vSeN~bd?W%uAF./ZL$TmjY32nD6SVO-H=v7l9kVZW^PPnk*<9I6k,]p/lD[Kt]p2uP$nXh0+JQko4G$>H*,>1q}C>5qDT*L*j/RwfCL7)E|m0s0;}?a@i/=/SF/PItZ9{gJvNl|u0*&(HH%
,f+j]AxD6K7UPS$:
Z,_zL@awE)pL@;Z_NvajPG_j.]7WP%wbx*]p*8w:#k`r(-T8`;)<h82aZxTSO_gV
zYO#a3p&b0yD%gU_0YeN?yxWKsMJP#P^TZc["m+t5
ht!020e"Di!-,1S#njK#u@W%<]:);WZD%
L$ua]:3+pX&J
[YR+DZUWf"k9%_5q>KLedMA@*/NW=.$~D[8Xq[Y|onlt_$^hZ7yRQvFEIMhRSH8_<-WQk&8[<lChwC;<kJwC)jgKrG8x2wS/-TqdY;k51v#oj%$_Y,sneKD0D,(ee92QO%4AL3IMRxV!DXrDVcR(f3B8*+B<-*
pVEFkCO0jK1i$idW9])G0qyG:8_uBd(';case"no":return'%Zu@B6KWB#?^-lM$U1Um%L%2:Y4jGYSVZb+7:F:0w;5>Vr"HLkvy!778=qdbPz!.%jXsO*D3Sx(>8NLg&.x-tXwEDa2vjPLoq!y:L#@B7(kue+X$WGkF+Low`m;Ps3Sc=@P9zf,s12AX!s%Y/k]og3k&z[Y`@%wl}gLh^F*6He^9T/!6VMDU"[]VrlpqoTz(0h8d7]7[8FcMB7^cDX^CNxEIhu!:1e,x8qjs*
9_0,
#`VuX(elJ1N1sv;3Fp&bZ[[yPXZa<
N3B/d`(~.ASJkMM}c`_d^f*bg)/dfPT0`>;DUOlsqOo<QR$UnfKX,1a)rTdM#D2Be&7d;%cG%s."bY:UC/
8*W[^d8)|)8:!@pX!.5J]wy0`j+L<,:ECmSn$wQyb>!pqdiM~nWca;:p7+u)![pTZKEwW@4yZb:Gl(&(n_9szGyF4P
d=.lI6eYeiRt-PT"is5]e7Q,ggN7t=h~XBfNNl!9@9:O*=a~F59w1!1O>G8O;,di[uyvNM+PSS(imgJX;1Bb.zykB0-+lw;N,#]m_j-MALYI^BxwV;#sf9(i9tsYSyGuyq-xq#]wQ%BUATMa=$y!m[nwmOd3Z#596$ecqC!Y&LNUxQ<Wxtgss}5/MqZ
L$nWo8mr<Ketx#"nU?Zf@<fS9+4
d$
ggsv;r,Bdp=i+E(ktt%.vXc9p89)1ev[="JnJ?y#}H*80[8HQ?
$C+Vn_#mY<,Ar%PCMps"*PZK]X8b,*6N4u[h$d0%80MdOLZ@Ql62QCSzx
$hT*YZ*i3%*.m8P)PkDz9%@c?VW#J)xEtK$(F>x~_3/&+_.8q|NA[:jc_Ostip?)d@@sr:KPQRRr6DdJ!gtbD
*AAd)@&;0Xr@J:jn7d$q!SG<&N3!L[,I;@11Ko8}/S&Pp2anXV*d;@+`!GI9X<.
IwkUGkbWw_qEjBA{=F/9_ht^"V39DJRL[=k.kSp6Qa:.ZZ/uu60EhhW<b.GqIuYUvZ[*%)?!N!"{DXTa9(oDKWM]Ab:m:H"q(FRq^|c<ZOm[9R;:ONbeR6S,N*q=SMr)Bq*y7^Dn#GbAy$pp-eNf0Iexl90=Z$-^jetOfy;,iw.k^dyd-KxkUI)u#.n>d,edE
>`II#"AN]YF0Y_>ots)P36TY<qi{"m(`7}H|ixW0*1Y&^?wcb"6x1#QMoU/i,c%c=NM`iJh6x~t"[MB{/<8ZdRV/C8gC_k<
vJNnx-dio^Votu#-YNiLM}O}G{HyGJul2yw>aEhqR>j]J2d#1;%J3))<m;[>"bBMR1wov68.BHlxR6R@q[/NA].Hek=Y?|`nuK=el#A#Ou#2<q_5gk*X6IGx00Pt/px"CmB`Bl+aCiAvjn%
Kn8&;-s)E7pa)p]?&ubOA:!vZ)Um/!/l#wI@L>gDw]HFCG*qq.%BA
N|m6#`<6t9fTmfm}>1p}thL(YRc8aRq?f1@f+7F8=ogs/PNZ1kHPj*[IZ=]J$_P~dIP(<^9|kd<9NWJawYt_gN@T=&T]y_,
)Br^oq#-s:xF"m/d%+mDyt/>?7p6G*m:f04**#y8@ZwO^y_kSuNDi`"g(4+=
wg6jwC~:jL9W*"_+oDJ05ds8";dfvj9!blFBJh2o?-)W[[~q8sT(D9x+ju.-e$sV3:XMB*FlrStLZ9Q=!&PI)c4$<rZ.%_s.<lFN!V9S$3NTB^ONY)P5_YX;r-4E!>71Sfx;/oq-apf*2*@E(419J1{HXNk%J9*yGCRqwn#TIB!#4&lLn.x>l
[+iB1Utj-:Gsw#NL&Nc`Zi_x{a~3C2iEjZ@l-&Dx=oPeS#*-nV+F58[Q):^=:u.`N,K$#_BQ6Eg:#Lf[R;*RK^(4R-gTAAh5Y</;naFA=bEg?F4YOLk&IB-UCS}6_a%TL[m_k5iSa$g8lwb6W&Fgz0T"|m[*P(%BgG{(hNlDm"{%/BVX+?%aRkY/G-2LkQvX.eV;b^A?9
2CofmWn7_K^unesKO[GVoV"ULj$GXdP=fm?$A-t7|?rXNTh0qM7Sf.fo
nQlaDqRC2ke>5,/,X~5x;2gc.zY/`;,nt~oV=gZd%<
l:n6{Cv`XNu4<)Y=%2dP`s_`20~Sfhoq#+OfpsC!.G48}-O:E!me"OKOHa
D[y{2f#$A*gOyNVkJ(w78:8[pvi!d3KiL<TL#v?D+8hzFYf?=R*u%/"ZrUrG:6X
)@qUv}VAH&:b_UA^2?wL*q+F_oppnPO>dDM@@=S>A#NdZlBO-Mp;
Iqkwux}5z*j5m*0iR
CGo4=tJ
xUoUdQTOb,W0D`-H"DnmV-Fez"bt?"D72[Lo3/suIN9PUZ;.Oj&
^>:40UB%*aJkn-
X$DjO4`O-,bI43E{"`4y$;g#VIg9KZVFSQD:=6aF+*Q%/>oA0`x2uPgz$IKR&iIq(z%S5L-S"J>X8Hk6"?e5@5jDL);oqw8A[c4GTs/E@r[S.oLRgoSzY3>
?j0[*-8ZAD[AH;9A/RJQ[EASbDf0Q1TC
NdZN[JX;KuVgbxG3ND;n?iv=4Kr+4_dsLSZV@k,qf_W!<y3i
Q8naDU3BG.3ns
@qYro~dlS&&I^KIiq
wCF}J[7$N2$"4m-qT!^VCl6Q),mp3Nbuf#:BRt?aT./S:a]?v|)1.<T|a8F1$zZvj>]]
lQ!>N3&+=nGp^wCPW`HYa>D3E0w/U-{bC%#bLYTv[]=[ItsZUkclh`vYk_;-:8Av?I87[m@]="I0l(YVjN?Z22{04IRTSXg[iXh=QB=AEB)X;,&XcW|SI]VDrW]8hoNP>xl#Fov[9%QP}-yq_^1da3b[2g?ID20nJ&Y!jz)@0/CT]DG+~?,/tA4a=yGUEUBO
>FcY/[s`:".0Fn=:IKY2)N6X@y,x(Q8d@a
9O-[l/uR-Y@89_6:b';case"uz":return'*s`;:h"+>$#5$lB
&_k2&,FS1LDlJGD-z`4SxRBoix}cz+_`U>3jL=E61Iw#K3JQbg6ge["eOx+ZAv]:&2~J306PkbpahokF6d.R5F^
DNcL+XB8bU>:8N?e(P0kDE<Jq+AT<xMuyUe(f
Ani?zu`^8O3?to:Z31ufb/iq=5!gf@d,cjB/p<$Zv0cR^2=-b9f0Y^B,z2,Y"[Q04"(/|^Us.xWw}kd?|`U
3y-euSR6b
>
s[I8h!
2y<*Jgrd7gUdq`7,0~=/[.;*4FR^+7=2LU;zG6q7)kF-`|U6VPvP*5ebB$H8>/_K,cRwQ~[^e.#+E{hOKu>gYsj9tiA?4ue+9+rSoYvTy@dXAolQ4CvQ9s0oPXvM1)GC0*J>Fk%-L=%F/pn+KN_^OdqR0ZY1KdfeN;>HphJ^/#]b:XDxkxR|Nz^P:onwh1<b!J"0[6e*?=G[^Wn9=}DF&-<YjNV*/@.v9/b&+-$K2"v",Rc2%13Z<0O>4^*l?,KAKX39q7)g&-^XDS_rEi-%7Ebiy6cy-K?WYWZ:<XWCM_PD&/.YJn>4Z34Y=zkwd6
/@ABoh+cZvn0/;"IE3}>(hAm)]s2.p8[<_tOobseA(3(A9%,dK<Z36bJ_8c]])YD26F!X4O2g9&^)76yH=>VdexRy?BVjN4wyRsE{K",]%iL^gJazS]@hg40`(L,:Mo8#;u>pogk$#5Vx+6!!7]9F6]?=>^ycD|(ll}>D^Sbnc`4@!|
*gau?/]58/7qY<3*^@1Hc:}[p->Gv;n>>QB)+
+v*?QZQR,B5E1FL(e
9K4=7"zjaHuNA
s7(n:11b:`Ictjs`^law+=z$FiefU70gcOadvr/!mN~+e]hYJv>UfZ~"zn)H_L_oT)R8gZ|),kvd|h/$Bt`Bowvk$1FB
)p6#f]r#f]^lZ<L|-Z"AuI1-Y"`,3}>.k<T+H$0ViGh%$H`~>w!SuLj6E$qko!df^y5^P|:1jb%glWw;YWdzd5#-o!:_,vO5Jd/VU"=9eG0FYH]tW^a,UEk~bNrrGj-F9^c]Y|QadK*uP0J|+q%.l3O*WnY|+7DRR874T@S,z)?r/f[7cT$1+z"O0C*vz)i3X4eK(>Dg#"(O1sKMQf*vmxD?e<vrD>>7RTVP9F(79+B:2z.;%Ld
tEW:Qa74d.ZRpYY)#,F<kB6d2a%|>^b~dV8KjbiF7k/Y8:8XIkxtKv&v*cT^Y$r"O?":#_Y:L<6/?M@N&Pva%#]53eS@bXpk.*<C_sic_
fn9u]3L2WOXcs6d,Lrj68{iAO-sGmT(XFSeKv3oM?U*X5!HVT1O*(7!r-?n[H,aK<}ulGxpC?Y%{GLNkQ2?E#$I3@n8_N7atuv`FN;kIIX/"-YLF@~FZs
Jq#qp~@ubn";>$N`(M[sB9E<o@@j.X&!U-:*CxnMTq$KWzx#khoy,W@0e[#iL2&LW!b
>.7zX"IiM&#%1Cnb3J=2__Qzc>a{<A,~03HaoDYlueO3fdN
:;"qU]-SY9$P,vsMQj)Gt!Ql99TcQ"r[i;)[mrut5B,{V7Bap?I6cm%?LMB9U~q.MhR,5=(Wk$M*UPgOJgP"QV
?*gr+0Rv3&s0meBMM(z/]R&E:&Io#t|hJ#
0`0qZVfuU=eiXFM#pJR
F@jC5>?wT.EaOp.RuR?B#+S]#@d(-zns7gwA.UVw7y(UN?DKX@%)Ac2Z]x]}Lt<ND!?#B]*o+%!,)1#9;er0:}EAsUk6$pi]p#OU!fidtqxo6F2(So1v!iMy0Lq?JDnK7tFHUF.MC1tK[:_pC?Ccd`cl1
SsuqFI=V#A[|DRG,7K5ig%BnJ$]=Vbl+,rX2UW`BCB[#&PCy(2vUN`s?%Qv*>nOzOKJ-vbP2fdon%|H27}J1dgGeOdbKNZ?mV;dGf*DyR$.G_eyc$c>Y6!%l2l:gI,r;E,`"3^P>yobHq*I2Uu`tN?d/GFhO+>vkWia&.|6|Bn8Oe0Li`)NRZX,DK@`ef~CJXjuiBY;I@~,o3Vw-:r0|7l%44WNKBT#o5IPv9bQv?*wHD%6r96=V%|)%?(a)TGCE*]jowP/7HpL{TYvT?$j
)PZ`(
ic=8y~W"84MC7pt`$bse7Ox1_]B/sx/
^
fsRo4}U1hG0kE|_+V=o[Y.o<WWow!e+i?2T9H>=;Osm@N7yz?9w.vSYx0{PZ3bU[j7A7#y_qCpJ*LN-Y_fy[&cULhHBv]vKX4#V_$OGF$_n,)V@seNY7F}
HR6uvKG.V>76UXu?!rb#z.2R~/-aqEG/WYN>0$Z1Jjj[d80()OjB&J^EB8~P21
p"iB?_Y>.0_xw*ki:xUuG/,<.G*M+fg1/0pJ#bV~qY0IR*S]=XB5YDH!=a($K7k)U+wj@<ssl8mmL]^lK[g65+(,)!KQ&GVeMa29WK1;#KE:Bq@HMIKS_5E%ZQ`ir38H6LxA.M:J7nijh/(kg4&timG*I]2.2L.NGPrGCVa3[A7VqOIgT
3kQ<C8>T2o&uf<)7FLPLH^[vsAP`N)2DP8J(MwrWL8@<T!wqtaRuC.:h5Lc2VcZW?Ek&/T1Rw4KH>a@F&^CN6_lfdomlYnQDe8iS
lUOkuou]Su-L
6kCh$"<>h^ZtDd9yOmM@W5vx,!:}Kr5"jGh*twDW15mSv$mM7h/5rJ2ekM5z.+Kapu_MJr/u-4Xl,v[/d0S>F{I_pu/aP|@/txoyF{#80vh<&s5/vXx2QQOn&V-?6h>?[|%e5}i+p)BKFDq,1MB}ju51EpQh1YF2gZw~=`c(L"2Eapf7n5I;5`#MM8cf!2!d(7UaI=q<i&#:.5>G"WY}L5rl@%iH@b+PD3kCCK(Rd#+9(-L^W;F]!!hnSP/#CQJ9X|$h';case"pl":return'!]^@qbOpm?V!$id#|6FEmtN=s>.1y.
(jruCHd_-r1cP>P>%/8]UNE{-<s8Sen*S*GgI&ih7Z/fck:5T
_2%BFXp+TrqN^QWoCa[HcLiMn52jFfn
lkW6"4i4U/.}`pFhH5yCPTCOisY%LVB)vB@8M}HnUmbXP~df8A[DIe]VIr"UboGzu2[4,g`Ok.?a$M6nJdy6*H,qpI%D?-]lAkUzp=)};?6]`Bfd]"z)`+l|i[E@"&
?X$^?%^,vhN7mOg(4Sx
*Fm7B^;4NLP$:!.yl(BBl[&mpUnGdh+][Z*`Hn3#@MSl9BW1Wy_l1Ypf|*Y&mx_]-#wrq)QbbTdc;[{b[?sXx(SDfscWN5VKUcSWWGh_TK;b`iS1UF#6ocBZgYd6;n"DE"Sc"7nS2vi@d2W!>kY5fGD1L
=B?h)i.Uv)cx?te/|roS}&#$m8V>>7g%[z)i!roSYk!H!/3nJB5#<Q8`T<h>z)~s:>*Gw&[PV+01nL+?{Z6X&rLl,fc=ZC4U@voN?/VJ(Xp9D$=XF/l6TgBoVO9A$J*LC
eH)CC<MWbG_J)"7E_GQm)#RJK-nHinb6e5m6w0|WR:(S
K^:%%<=c=_
x@FFskj^sP?4s!.2Fl;!C8+:3d^72;)Qas6
jx|#>`W8Q1ewK6euW4lZ)]Q>:gjoXCb@.@8Qr=;jH;1*+B_.3rT1)hrM8f2jmvL+CeBuW:vO5A+:#d<ce"a$M"1Yk*p^pdadcwTQ~`v*WH=U&"KH&A|96"*
c92Jaop4;)<!.?B]O+|Ffxv)6*,Wpbd]Cn<ZAV9&6QZ;V
!=?:iG`N-3vAH1M?)Nuo?jK4h@:ee:1
9PFX{-6>ksC!(KO;cJa,GwB73X&*oX&09BbiK
_&]6XfJfT9D/:-PC^udZu-&<pe5PC>d8js?0bL0Mq
%PaJ}6@_?CM.1k=w(*2?SNi?Da/1mV*/Gk?K3ds5)z(Az4RobDdu
ei)#Lt;Yx*XB1=w}N6nlK.W]ymJ/1(T/dL7DFp>O2iH4HVioB+t
@7
c35g)v
U1o>8G%fo-")MpWLd=^7mmtv9rMRw[isks#mi@m!6KBvH/!Ppv3x-;"u:&7(ZNG|BD5`q.S<_:#+xG>>]#sWX2R;I4Fvt8j4
PrQCtIfL<RP4)#&vf-4kEM$t.n%J"CJ(8mhQG
&6BrItLYk7qa~8ig(q0+2GC%mrsRrqSb0=tt;bFUZO:kK6li.D]0I_7l2!gCn!EdQi6O/f+?^BJf)g;-~4`h*JI3AF[S]L/Qqz)r&xcqPT8R.63rH.z$U/o98]Y+{ee=*ZlsL&(5=G"MAivm^tB9|*8srwq3?B(O5&Gmg7$pei5;h#d[/P2F=IM`d-~%MtQ0MdNu@:!y]slsS
~T,g5-o4pQiVJgo3$b`%vmv0uuPjjx<:bhz70IA97vVIoOHi@RV]..i:N+)w)FV*wu%iCXax:R1<Du#AAcYtr[YO;&|$ETc>1;PgdRCw/W!q~:PyIZ{1F*(eppWUu7X6<r-5;-b"*F^`dT&1+>F={YJ(6Y!9Kxt:?tf^G0uX^ahRxP{Vf$}93Lb5Rg1I1w""g%/CA/lp#mZ(0v(jZNa/mbr-xP+e3r!.{FzC;XIGyj&.r*)>1_u@4?apNTU.6nVv.]=j*c67}Im7q)5+UK.;CSeo}V.e["B)=>E0e-K[WO}O_nFa^AYa]5}.DRmZ)vG-3NAVTy/PH#4!}op,$R];:$.rsE.bZMcd4UAj;j@R1P!rWC:-6CI;z+[R](<Z2)KE^t[3tfo!EnYN@O`@>#H$}dsU6J?4qreBeKWL#<I=q!DCi]pTTH`Ioyk!tLht$>+T{FfyGL2Nft4:Q"VDv`vUWCc
f$>yFS0dJ1ME0M(MgXw#$"6Sh,)5.g1`BEeN~@8:(;_73<`2ixLNn?pbr/r#m[mg$m{;S&00-"Q#f5*>w5;DH7d]
H#BlfoGh;edc$lj*gAv
I.XbiK%1f}j]1FiA@=GyZq$_d7rpVvfHOY7;6l-8Cxg-naH9lS%}LsN0<D9?o5_:#n8i2]cbR(p{Cks]-O]60bU!uQP`4ldoV{60kmPC
f6&(Q?1u:"t-)p:qZ20S-OR4Wpg[gsEp,%!Zp/&CQ6ldLa63vT;y!0qQDenFd%6Q`_xfIt"$9*doJg-#&9]):NGZ,3X[g6}d
<dSR]Ek$gC084BdA[-"ysoO@S
azpLo1;X%a4U$%Q_tF>9?4P]!cto,s8v#>C/`2:p_N#b=Q-7>F+H2Dc4BJCZ<5$!e|GtP7O+N}jkTpSx
V6HB"A=$7EKM]lj01d9sv>7
`_klV6LgQV`ur>HI@XiY2s4?+v0jugAn$DPw.6Id^_C,f/z"Yu3<(?UlRj[oaJ%#sCC70T@!@OWoVT_MaDGsS<h?*]aPvfvL]4mch]z#nsF<*]>*%rtO(@j2C,QuaN`4t%&6-
UxIvV6ZYv[l/e[U"V4)d#7`)g-7KP>{IxmgC6.adXU]>q0+(lw{aLQ?EEj.A.(eVPt8dWVzSdBhqC
;(6s,=G<Nhyu^)18jZ4_8<j+3!S?aqVO?,0x9o{,)wUeqq>@5^H%EW?-Sp~c;lEa3CI&HRtS@4:o{difv/_45XIs[h;&j*$F
XWt@r&,*&@^!f?B+`|u:>sqXt2G9B__R5kYKq=3tD~g@3_kR"?v%&QW5N|)3mJU_]leZ+apJDg3k&_n%sFyn`bJ#v=Qf<JXu8mN<4&V|)@47Ywd!*/yGJgveM].uxgic*hK{MO[@O*H1lV!i
0eb?z=x
F-z(:(Aqt.(d9nlJX.%i,nq2N+%+CvB0]>ND2av6.ht=[=&wX0v2=V/eY2q#/G7N/wYV,JabFQG*of,<;(.Q+
O,n///T@mZ2K$x+SQxxT|fe=8;vRDT@^MVHK.iv=,S&l{Wh,DQ;0~?{Mj,Q]85>8+jZ_l8NtujmKVVK-ij/%c8`kkOZ.fqk"_8?$bvjVv04q;nZnvG]i2Q<)W5GQQBsfq413`s5`%d(-IRh`AqDuyH"l)wsW(O|AICO/r1H-fuofD6)O+RVwO=#p(Yv6jq%M!N+2Sd!3SKc(n<InUVEd4XwZMrPVeS"Xws?Ys;Kpk^PD|JR;Si2[]nTsxjh6Vy<s/?6`)>p(Tk5@#PcRA<R_1eEsEt[yJ*KZ>gjoAGOWSQ,u%P!Yz(fdaq
a5PkDm4
WWqiCn-6Nun&Om3J1g=~w)N?9@KeGklP)P%:T
;1B6QonJb.VckY84<CnhDB!GG<eOcWV#92]GTd4`xbe6A!ls)zGZrDC_$QWUs`Jq!d;kvuLDL0w4Qvkoa]"qMGgP2UF16iyh3+J>=FQ:76Erme?d-@^@D,Lad3+)7eqoCF-?PlRkP.^xS(pX;J+Hcl`RImM9Rm1V7kl{WjF]qjT^l6-zZZuv9G<p8=hqGeau8+rqN;o@GvK5o+P.*p+@-iyEQP@DB=:BH?,0^R$?KG_#$&#S9&9b9IZgsY:
wJsY,epWBF-(NS4m>h2Uq3.3q,7N#z`FU"lVMm7a6O#T*FXag-Q(FJ=BF`"[]RtW""';case"pt":return'#]^@iaMD9?Sl/
$%CQo$0$%hHj{TY*X"8%:6_vVn)PDfLW3o^3W4{fuu=3%@|NnP(!gW!mbT=weqj^E
x&pEqdC_=vHraAUs|a}yXg}8t8lr^mG5Y=:K9.Tb#Rb7A!)(p8v2nLTY:J7tCd
/OXE7#oro:($Y^Q+2"ux4Js6F9PNST,,T"h_!aBkv[#_nT^:vDK%w?$S!}VT6@rPfb!QHh3=7"`uR&7.]Ft;?6N,]ooydk[D]Zz)XoGR][(o:*yIo+.}<i(z6lN#G?+X`CJBEJvkgPA[@Mv!.nw[7Wb4R@
D:UfQ4(h~Q=+gN0`<,.1Ehlr,r$ks)R%=W7`hye]3r:=?`$M<,^:".H]NWHI*f$tvB?[3flK$g]:&6I96_WJ3t@&uBdR>%yM=`:m&Sl+B+AA(X
IAoUyoNl$dD_:mMQF0Csk=PcKagfsS*C3gXAfI;8a2"v#0BkFhYJ!5@QFnxC]sX|ofZ_2<66%E-*9Acl%S=<c^d,[|7=hQdGH2^XC3,@AI`+-L&xPrB7i
5J7D/>ONK
5#d%5Em+j/gdiQ=;q{)EiCEHv,/W^cH(/N+/g3Ua^bqw.2K?/s[jaQPDQqs;=nRa7tZ%m^RvljO(GodrPkoE,[UnS3Z*3RUi>{Z$F2UogJlFg5,,R5BxraPJ
-wJ#dv!Zde#h%0=TqKOSVX+qrG=IUVO=B2IJb%.TFI&`KHHvwI.=/dtkMEaqx;Uy!,{&PXA=r-5kB]@$T#6BhN?oNJQ7fIZBRRAYV??(MWm<!o8NrM/@v0ff-N@)B$rcg/Tf|wBEaaHn#8d<0kD`piTPLj7TVt[k{=X(ylQ_vCjX##r)!1_$q93?dlOK<;JU_8B:s^Kwy
#k8vcsjC
xV2sfV^7SNQ5AEB`7m$}Rt`J+<i__rQ!u4b8Wl&U8nKo4/q*jyk9[)G$T$A,cBg>q8v<l,F
*TI9[6n&&]GI6:f4o(_FBO39J&vOuvNSf%a0(zlskAN}wUPLK3T^oI<"VfJ>e]<xCbSd67Kh`p*-d`m{XXF[&{H()hAsFU@U!E4H8x`?s`JH<Qjviv7B,Mit>;@mOSC6=p1j.:Gy#hanwAXZFaknL"*oeFtL#=(COABn`E!2jk5y&ubrMre3DD5{m>S$UD&ZXWZ>+/#eI([y1b/8nxJL3Q>~uL)y=PrWhNl&iBtM$Or7Bo#l-A_y-$l`dOL[/pPV#eFQ#A$xNDN/8RsjHG9:BND,(7f%05H5!(fVvH**Mv--=!,]d(mby>PI/:.T;3a>cO$;IeccM1J]7@gmP*jNv!w%.Lv
[Q5fEfC(m!3V2>M,]X`n9gXD>oM8+MLlvhoDa#t2Qee@!#a`:]lAW&PeJK_|8g(xo.I{:+a3=WHJgzW]1qtL<{?}9Tstm3PjB/d(it.{(|itpn
>B}d]E!D:pB0Xl3ygg:NpH[v.Gz1R)MB@TsIM5};B867hjuFh&K23Z>,AMTSH$T!o6<`|8s!VC</%NhZB"-V99y/B.DT0B)qovp2?bc2mY/xil47h;k3?<z_D%3S6)6Jw"=K{YgJLjK:V;2jd0/dXIQAQ6!B?>nFcj#N-5NfF5KVG]!BIfuPr"Wyma~QIe!VFfPN"+y<ZLftQkRbMFEn(A]KSUXB)58dnr-09Rol!i%cx-F"T&i?)A(7]!&rLgSE:
`
J*GLX:2Mu8HY<oqw^",Qd>=m[gzv=sLW@66hx^g=SHjObq/"duRu$x,q5FWVkM/0~,724v#o47II*
5D6:!,bfK/]<9Dd%OYw]>T:%K(o4Xj_LQb6qGnZ4)x8FCImi|+[40=W-D08jhSTl`2uolAwfC1q)*8Jw.SO>e!bQ}>"A(U@-A%;dn!eX@s!&2D{G0
)0>:KazJLYXb@c=`+cQ
":D+<-*ZWS@/d,QC1(5lzmA;9s4u)dg4Ylbm/ybPvv|e>c+J42./4w%$ppd<}6*
Z;rl>v[Ok4N8ojn5XFrK{BvjU
Me[M)]>B^eu9l>*`_K`]jA=T;NlWV65?h)^w0Y5qY`T.~-%;qKu/
YV<
j:p_b`5..,A*(1@!I?oLe3G7&X!vu%!YxD"_
"]F_f]#D5VBd=
7vD4c5u+iK;i5GtC#Bj1:3Y%%v-pL&FX3j&W)</S2GQs#S,o(czfd>3GWYi"Z8}P^jLQ:?x.7K:_N6,OUX/D6C"r2hKq/L-uMKR>R43
J$}CQVoG?$~h!HyqAQE7Uyu,_jhnN_sqH.Y.469/{N<A(DZUoZLo_*ZjFL-H<LlUy&
Jbhx9}0AIO7K[],WShF)=@>Zm_Sm]Ix/p|NOGo[STJBw
B
,pXt2.IL$IDNLS|iK-4Zt@kW{n^4TD?o_m
IFW<G#)5_L2Cfz7r$ilck6Xni3W9D^y[^m)$hI]eAF?SdUaipRrO1E<|U1nTC`Z%s?lcX//Jh_FvZ=n`bS;D4>uNHFCG%Mkb#@nMmM^|tN/`kQ_`ah[$Xoq_8(HR8{+k;+$,.yG:1h"<CfA!"8p/js]KT<>(O;cO[{M:+Ca|*IQSx#Ib2ZuhDaY|`dd6"VbKmK(:vKjUsKr5?}Y,_0D"!abiem9hVrG$h*x?E9q,57`Jc"F~rGYFns5#+tVz[)*?5(8O"n6)0,+zZcxqUpDkl{)Jw;@/_Fa<c%VaYvjl4|LJZmOpA,-]F/8N_}<eFgR{

^Tn0?A&d#7TK^H"XjmgyG~KycdZfLE.CZnd`yUqH"#Ydo.XEb#Gby]m0yaeJ7Lraof$/b{sOTeXw@WM!9L)f*Ja(V*`fjw`p<Kk{n>W=qcvjkY<F"|lC
{0dQ!*]GxPG=}qEeRUqYQ[HTv;b5j8X(JdqAjR#@r55Fg_@W8DU8VIX_2e6NW&B$%DV2@U7i#ig%dV
A<
D*!c9xC/180Ai;W%5L6b^:W#w1
_8(#F_$zEA@=
*;9wi!Y@uf=osGpa#u|N&uN^NAC^?p8m"ThjQ#z&_05vvKR3%ossN0DEOl#wRK0NBOEau6-kGT{QNrv5rU*K3Z1.jul"6U)m}k{,#j3%
I0!6Ytr.7P*jCx5pre:8Cx=kId*,dOy&Rr=vG}*l9/>Loi1<$d/qpA@F!yhm89)}Yf<5Sr&n6w(x+Jx#/>h~w|<oOsjowKx^%`>g:;KdvKXs?W';case"pt-br":return'"]^;;5Hp])R*#gW#hX>SWw;D2&v7$d2@PZhn8h`rokpEQ/KDZ)mXL)U9RCR&/"gVNE?GhJ|(mXr9OSS0z;/,14S
fDtxla^V`,EywaQ![TON#=?uqe/d(=bjgcU$P%I[Ew*"
rJn-n`.,,]47p5ip*J-2VR$x_v74su]v+!XB0k/:++2s6qx^*q7.MFq<,id6+Z#v;kG?(Up5oBwG;P"Ek0G90Okol:sFIX)ee%=5jI1E"a^Phcz)qhU6La7580!O*Tm2s|8+)1Cc3,rs_&PF[oYjM~&rwVD~3}_LIItWR-kt>O`pOqs-wGCLN5d=VFukImEpfJD,5D@W&+Tovk(iyD6
jLb[0<rD8Ph88,5b:9qSprdD3,d#]v/-cf37mbm3x[X>P-_1]`*ySgIWW5q/-ZANJ
r>*B(bY5(lC.YX38o5g_w$NG5pfKH(P#IjWrM{"3d41Lii]sySXS%,5-gcVeu3&5?Z:p#Iqz?2&(w/B",fFUN4IGXR*1o/JEL2)WptSZjW!$^NCn281^(cjg
V0+O
H9+rgzb]rN$#pLL:C:,f0xl~Gpa![{@jm;J+8!c~QwKJMOHod]G>mZQS5;
|#d2cu=`d<C>KFy)H0V[p8&3>%9ax$(
2;@nLdw#blt3z#s+!=*GAe7h=L,fJ,<ihwQ@33,hQS,)(B

jC8AjcpW^7xRxr=VGOw*vL#`.vF##e3[
<IpN0`vONBOVj`,/-U(;f,c?o05<Nu]X+515/"t[0YR!&%9f@R!SNPlx<R(;RrFGQVP"7=0".uCH*/bNT
N;d%%PC{vy(UE!,/-|+c<8!+ewClyIGpC&YoKYZ{lT[MPscU`iQ}tZ.yF0NYalCny-PYU:J8_Qh_?z1L8N:w4y<Fbq0QB3/]$ux]6Xn~J9N$k(eX9IEK${YsS>7?bXYofySw`Ym>nV)DUx8#<=J;#V5jbrXExhW*T:)sQXtRJ-8-1U1VijQA;w<HQSCGA!Tuib67bEcZ%-f=lyN#`W?zY{Q)?o38^c=c[(//]QYMpzc8fV*u5_`dVe"K+?/D9^J&e
5&Tl)LeY5j7K0$xqWd[WLM[oKKTJ6Hg->J,M4oNn$>#Fj4&SYw*Bj()pOHw]^y?i<EqLw?G)UKw@ve/S0cqour+lFktLb`+]G]xK+P2Ge@&`1gV6*dxd1dOtkeI+C?$l(MHX>2g[Wq>Dw<6-[a
_K-dON%skLtw|.h&_G>vz</*xY#Kb1nv!?Rn][A<@3KrBJk_"@$*z&UZl3phY7];HNhvG`J<=MF55?XB9/C!(t(C/5`CgP-^t;R55ELH=8mCd#5F`^^ek$KB!S#cfCByKg.+#<1X]aNy,#"*sSSrD5UXf(z[(dZQ;TDF[1B^2L9Wy[wA-iq`LMf#!:}ixbsa:FBMS4^C..ElTUYU@]V.bH,PhUCteK5E*+f*{c[$89)X^Plgme!Bn(!;!4vLVT!G1T,!OO](zBj`rFf$/MC%nCIP{F^Pd!7[m?bE0:([v0,%Y:;o_8O;&Du#ne@EN7U;^n$dpExJYjdqpjPG:((5&(6Cm@Tc"kZk^fi
geJ@T3+W^i|7(Qx>]jrJ^X+TSpXn<PGMvf<cwkc#(K"R3YSsE46QC&**qm*UjQuN{i$CfKn8P-pZWt*=tHTlli+XZ0@3Ys5ZJ9LCJ%;%[Z?yT9>6WSc>]>KAKq,!LYSBUcf.+&uG-Q`hs_VQg3d]DP|`19/bkkP(;#DGyKoaT(0UU]%LAi^O"]S=.J-FFFm2K-D6BiES5K
dO*zhFRg1Q*0d=w.SO@+!r<aL
/v+9G@>M8E&*Ub00NZ@jk[i`U2/Yk*b<+cawG)rb:KoyAvQREnU<cwE/78oQr!Z617do4O:]HCe,Psa}kiMn@FoJd{#k!Kk_%xRvDTj".Z!V<Y<{a-b}l*(|JrvM<@pA]Ds.rtP?cF&i^d@@B^os3ZikP-M&skuYtZ6Y2Lv`tf^A+Eo{8q[mCS!(.)J<oH@l>:X)dCf#D),Z%QyF+K=A:8aJ*3d),a@pGo*)V^g!Ut6/OQD6Fr!`/phb9Y5/4]1"gTQ)gf$o5Wfmd`B-E}sX_SxZj]^MBe3WG*k$6)h6G&][0Wr&ZgIL5$jcjfq~h^)Iv:mSL{Ha$dfT.7]
Qv(6QGRG%7S6GOfVe7S1Vg^t[)S_4ebVyJ/&Ze70GJQQ$Ptb_SZz2<TIZrYN#eG7:?gK%?Lqaovvm|g1
a4.]N?"&s!z8nQ3"@J6SHxS6;&h+VbRkS?;pcq[-,NZ,3Jo1qwgt>J8W/Ccjt,xC-D{?s4<QAJ|/AZW>Y)F`5*_B;XQ6i1hsBwf;#]-]E,Hvr#3R3*Sm)d[=n-)FoTScWe!>:_I;VDeBW#t9G2m5/_sp&K7c^fO^nw7LWEV6RslPzx;UI?coXmp4u%ZmO^zNFa7<Tj)c7
+yC?
@]<!M>kG?:u.YA3psXQ](=*(p]c5!QiX&^ed3$fwdINsS)"Yo*A^sE8,FgR4aUC`d3V3)|Fg`MoBc0I<+PAO<9F:q[
xHZv8C7rUWL1R-#U6Sa/jQ{]PeB0^4roNlvRMOuE8.G(Zq-[hS(f@`rha<}@E@iMYFLLO1~N;a""SM>v24!J^3GP-([f_Az`B7uw^
]vY*o]Oax3Tq%7tD>>F:*xKD6:P]s06tsVnR!k3[ne(y[U:G#J,Mm8Q^0Y-lLjd*dTq_l0jALEB>qBJa1M"J?*n[=;,V,GrgN:q_m51K<FK)Ue>qE*ds(EbGfgNA9HMr["K!]$|;ePMTYS"6)h?Iel3!(SwZb@nfVd
30C)[~^*-ih&CdA%MUt~lJZKfpfW0C
2]LT9+]E5Or5>*U5hsWg8A<VZUbF)U~qTZs;Q?J&21Zn|Za29]Z_HHv1
Sy0rrjCTeU8F+g;8,(92>,/6O-*"-;SYM~^ExH9oTV.0Gb?G+.M(R9*l)D[./{lEo$eTe(/dT0iyjfwwLLfRcGYB5D#0VM2S>hrXfr]vNrO2XCk-/h6Pi&x=#jw4(,1
n$s)9$.ycki7sw]}r+.L`UirUadZ(BFAdMgF.q/fp4g0+hJsx/yx:FLXdb+I
{[j@B>2Yu<bH^O}Z8kB]>L1^~l8Dq)%uJSYR0IVLF(MHe(Q33(bC.QhReNodaXX^HwB';case"sk":return',]^K36OpM*W2%ie#|lh!QJ7*UgxO98bC1H+NNVse^f~9p;C-B,c/Nj23(wH#G9g82i)2S9hKQBz;L
ekWl|RUlvLj0OJX
MkWMZkHG/r;xZfQ@%Mj.#Y`T^/H#WiP&4W~cLV]HK%fXu,rFDmJ?6v.e}IR]@v~-Xnm6;I1iO?7q,w0Lk_>K8Xg4YpJrz""%SYY
[Q,-^*ZmlEa
_u@5|)ivWMAr5x;3j;;x6vaO+
kQ+W/i``wf1WS
*W/Z9z)b;y6_{K%!+=V^Vu2Q]s_vAo4+o*QHb&
XqGg;2StYD>7ulTDWvOf$&#$jQec
|lE#re6C+Mh6HZit}VJw-ypeDfr$rr*h*2ggn<09spPc)OL*F?$;k?IbA[7[N*_rA`SvUI]7v$RTai2ZCkcRBBm^]L!9sBq-r]0c$SdUYctaK7ET^0
E_0.LOLJY2JR7_rIPWcA@:#d?44+$F1l$?#|evY.$3?OFQ(3fP3CA+)f`_^/2(Feg4^#%8SipYw&"b$F%i1z=N`c-MK>X)8?U&eD?kURlWkA?zv/#[v_E.dt3aV"A#d0+J%a&bam_#b=350/w.+J)Mh:]#xJ$~>Era:0Y*31_44(/Rw.Y69/0;)(Id,G`uG*H<v#gm=hlvekJT+6OdoD*-/AN2M:5}vCB1meBV0LdUbekM]>^o-Zl6s6^6$yAqj{d4
e!%0uWEz%<r(urXh$du"v:d[<Q[PaY+S_[@X/2.e0:Z#M06@73^1eU+oLdNQb&$M[Gz2,dNN48Y=d=[=YUK/fxM/q
<mc
.gfFI3jlJgQQ%D-b@^;REV}wLM]l`jJR@;<8!0Y)yPA-AKFR[H(bAOJxqyA)ndUuT<?V.PZNt
V5=Ls$@f}Q!67$>+"oKKK[b<SSzg7r|EM&49O]M:>4):~W=%p8:Yi
-F4VkpbLfXiv;PxO0T?D%>2[#dM5i
5?z!}^)_NgTdYp&
kEq@VGqBo@!5ek>lVkOOd?qj%T}DmEmJpKo2pwvv<aC#rEI=ghH@,$/$Y6NrzWWBfIJORDbBrDC6Jvb`$1Rx46R3Yld6Q<{uY
L`&Xjj(v1DQk2;n)TFYk~4?E]Bg7{IWX
e%-Qkj)Xi1NQ.XQg/COPGVg,,Us@jUj>R=7:*>@Lx6E
#Kbals6KJ_#>(eI,_sD}qJS+bD:.i)M>Oe
^wJ.q0++Y[qyPD/73,n.3J<8Z6Zg6c&$W`Q?I11NI_`^|9X[a?+Z7w-)xSohVq}t*W:h[nvF{W,y#Ga+B=>7fR;g=-qM?)0UX4SIGKtPR8%XUTWcCGY8bMInuY1%:MWg5&F55,0`QRWf&%5u);=;f_~6?`RT!?(e|c&+$;xdDL-q/CeeCRq3
yzd!l:lYmw#A0/Y1-uNE;6U3^/bqnmyhpz,qubCFv)99e?QMYnCYQ)u
H=Wc8
Y7<])9/XVPpRiJ:XHo:!lh9t=2br#q>2`vdI!Q_>7Xs)%7&7tW@30dA4>-TyZx+D]9CHWkH5UlA+UE*nPn#6gT!!HR0hxaZAWyqp#<cmq{1x?|%{C./.:qkkM
n@Dw"o(6`dSMl@<&h"TNsu2@48#T8GSIK~M9%UYEC:tzn^>Lur;14:C.GY.JT=N/h*fuN_k9nl@,ES%<Sv"eN)9C0q`P"M"$L@2X*lWj%AN&WRn~PX:rwJ!7PJEo6BX4wu%?IQ?i`o>.;[+*vcu&D;LW!d1hNR7k8jB=sm"HNUbst6xKQD4YaT#[&{7WFB:%WyKLCQl/O!(v%j)VfPP:NN1un!S*U#&N)5#_HS>v.(#_6|;G=m<4r3"cV^&&5J"O
J/i%|hI^mJ-3zL"+&d-Z%eURZ.F]ti.&MMGo`M_[g9?NX0"DuUc+K^p0:oA`|v019f"=F`0%`IvAG["#o[(WX9$dse(`lBq/hf9%|Jgh91G&Du.aSH9>gKm(BUt,w7!I-Q[@SNm5C;7bNVuuu7]%=@S&<!]BJu~^}wOHacY7Dw-NC5FR4x*8,m(*$"[$
8<e*:+V_=48I"8PiEd=])IX.l8;}wz7qNg<IK^mlY(kvgr9>&MWJ7Y/}Q+Lm-W7|CEirZc[MG5SpJrj:cB$/Igo8aUV"6TaYLx*gNrI)Rls]glI:&y$g.s*:9kG[Md)<
6U(e>%0u=CLtuAcih/+uGXZ(?8!G;)g&3adS1rGnWW12|mKD05w"A+!C#fzD
w0v*>oY!>{iOL",Q4VVNY_^9le@4@pYS#CI|YNBrx{
Nkx8"Nn4;E[J.[R8-CEcw]G5|-R#?SXoE)rP+bh#i_piOs]m3ga6FU%/
D=p#(
xL56;p7U*MHaDs3>b+bE
6<Ww3X1MH5>3MO^E==X]y2n(`</6<LkLfw]j`_hmcJFRhW;"pbWCX/M)?6M?WI&2,,0i3uQ0SBCk~H-b~6c-c9>E^SJ96e45g`G2q7c$"@t5C@H=qU^,ND+>jny5IQSFA!vrfL>-K5mCs0r??_!Tj]1$!f=mAb2:v1O=dhfP~$1:#.Z-h*sgzS,`p_%<4A
dMk|a2S#9O!ZtT5cB2Un^F82:F<@ZVBuNe+?)H,r8{3Fd^;r-B_+Dz$5NLM@c95a!/RLy9Ab!yp|_onA(//z!!Ali99_F=Xc&dmqw+5,E4Q@"qIW86V[foljyR5PLeM14kRL_<2CZ8X-G2oae-TC&DHv%,.^1S./(^6f^YWo:v(0$1D]E>h14W.9YJ8dT*cg(hD~OM4I+1XO0xu0.=kl&LtXWw88/RjD;zA3h55fktqFktRqdW]r=}w0f[h2EDK,uprbV+EUix5CS
7*d*8.):lxA[*QCu4TG6HeEu[$3)p#0bb]#%(/KU"LN&G-VeTbezQ^walI=GjHY-8))@3]6GVT@#gs+cS$f#Q>`yh#2;
3q+?0).dy:0:lTr/KSFebZscGXw9:Hw
c`T*:?KS"2^m*.Enpr1QoUDnG7<Y+xf5=o]o|v!syo}2DezeQe;jwWK-JXhpeA4+LU4f2i)f8u
;pWG6tiTO9M/nR>+bo1sZD50*w8&B(]WfV_w&2pW][YOpe9j.8Z6Ps+zHdJ&vV30ro@EeN^2&2
!MF!^Jx[fQ|o8/Zwgqt4`t;?%MeB;93J&nalANV3OM-Ho:NaF^I@`>m<mF7G_UTFbckx=bvhADJ1+U#R|q:R2XN0ASohqmASXdN3.jF.GDZ*le0B7Km[}JNkx*Q,w.0G&/H$riKwI(O6vrh+qVet%t2/=Y-BY1``)&GO8,NNn&zX[yi4oR09gW7TeJv5]JmI{Hv@DgwR.UzfF5.`Kk+)8_nO="!cw&e<4A`NziR-}*uN"drJG.cLj,.3VOZv1,z<3=-
10QUcqP;e1eHtU>$J481LUYldh(Va02id^9Qp-<("KurgwciaN8:;x;Ul^%6bfE
6,<cP.+M2Al@)TJD;;>Xm=}slI)9zbetbY;;#G6XhQ!HYntJIpTL=s|$m3iD#xz?N<iccvSqDU>SmTJ++9_tn/p@)HsI-1Nd+Is&bdk1QQaKspO;f&[)a;sy*o)';case"sl":return',Zu;:h&D))Q,SY/8mbP0+CJw7dzOPZDH&qA"Qb79X.x"(`D&N_1*b+eK3%HN3v"?UPuxom|JWM=*]j/WBJ~oTl$psiIDlhqeG_
bU]lun$5nV$^kbcLUqm"7Zt3eiV7b`u*]_,{]Ayz;h
x*$rwt3duR[Wzg.cvsSuEb15^i/2h?ft8,ErQc1br(Qgv7
8^mkh$7pF}65rG[=h%,}O,rcN[3c&djG
~geo(XaN%X"j:I,[:7OM[q@1*A`u5G>&oOypC])C4A~+~`J$Jbe_vcw&mc#0o):Y>,=m&f&$n5H>QG]&r?0I*ASvQ5nblum6]BW8&IDc5$"jS8u>7@IOqqq&3SP;>Ns0?KLGhb:-+*apzKTV_G}io.k(bGY@;if:ha,Q$H#?<RA?j`V]._nB6_W/os
>V$]*q`BOB:"88lR5pP9.Cf+dVZ7:PJ@?6*M5#4dPWwn-Ho~FR+-&+N%)nI7<&^rJ4Otsx4O6+[HNd7~MUf[wP/ro(s
A=pFKrUyeN^//-MdN5[;j|ag1TiJQzQUs06,Ie
,3|=)]M&vD!2RZX4=>
@m?:e&#p9PtE@zC&-ZJ}vfd$<^t=:QBTz!luQXvU6nyhD[k"[W%->CEs@@kv!Fo+V^L[7O=0onDWK2OEd4:f&aV*!UpC@Ek`Ev69$,/db@LBP/Y:n35F>,hSwM:q7]&!PAgJ+gclV2w"A"D0(]AY(+*4bBDd^AeH+F`sl+Kb
H"(6b*YTg_#(0_1iu3^HaN*TWoI`}
K&l#<nv
L;lA~WiBu6-nCbjslIXS_9
#z3f""lrl4lxY*?V>o]&c,NA-@,LC`M40FLPopn,bl#u^_MX$O0sA96H9>;VQ&7@OfUxkHE??^/Xtyhm,~"}y293/P0ms$Ihd7=vJFGxD/:d,ovKd4]:^kLf<
)b1-Si#$wyPOqG_0fYA18yKV;w!/[dt?o7ju&f:YL036;mK@5,tj"`]qClVC]iEWPROSEAd2#kc.4V88LzX0<o:6mf$l+9x6Dpm^QIW6h1X!iEVR_&q[&<Pqf>U

(-npwNU-YW>*rv?H6Km2qL4(jhAK2/PeWVS-6.HYfg=VgYR"C$h.-%4w!["YT)3CAh7F15S;%)G]`fu]_E=0g)Ll)_JB7l#xIgMffC`ANB5e_1sw$&hWXVU,0qoC=#L2
B2z&qJ]pxOcaAVs?w7rVsMhAId!Ls@grqMYryf`{<mno.<*7%U2nce
`78>ki}$l?Y*1^^K_U
W~P.:b9@#gI0I&)u#;<
a"i&/_o^5i#1:NxG$7y~l7D_PE9);J9GdURe
;A=LGUzJ!m[fz5-_>WQ+?VE+M%$x&x%7!r&$<46eM;tW6A[f
h}[cB}B|@j#{+rRI"if|;o.F:ZBH9XIt-;C]+M];,wCrjSjFL9tU?Ddx"U8$>}Ho,uoY*V&d-df4#m2B./*<*MGfZT*;D"9MY>SWTbBg_!sAYn8ubGjr*P#!("d3#9H$3,!@oGtyrLK^X<._1^2&RbPr!N2ZiT`]-yt):m=w?4f&<KlY-|tcQF8c*:aSXP7I<@7WmD
b3]O}[G6"v*Z?vYYo0Th|bJX`SdtOaljkBWbJI3n5iCOaK+4=6YwL%mHa!8S{u<<^d:oDQyG%4GGze/j4&te3eM3gWB-P7=c$ko[dxQ$>l,s{dJAl3VWxZQ=t-zgANV@;2_[%r=X&f_]DG;F9*QpzkOU/)mwY9)F2&{#35MLgkwZ@o=8bmWs%R_B?He5lVESu,_5t3-6.UL2YyvDWL[sJ3%pWT-p<OQ/a,v9*f{L"itC8dJ]DhZj[dWI[:|q"y`NP)Yg>VFXCT4#7S).,
"CmEd2qj3()G_X)FGg_38m9<9V^8d&HTbF]OdEzrBJ"+aV/Rb@_DvAHN[;~k(W@4*Ti+K9"^l6$tv!"&m%1">tX;NIPS.HoV52J]DAy18k~x43*Mhe(+mU3qG*LT@yR*p)4aQ.&Os(!@7l&y.E^&"N}r#`D:1BF*`7<pjE]B=;9cR[%c~;*0s"_K`"rxT*BU6jQVHtp,I4a*th@GFG<R2$Q6`Mz8kAp19ESsH<59;+cJ^ss"|C*mD(%n=Vk7A1Y%.ua;Uw{m,iRW(=6j6X~P!)8NAFhHP5*-&8-)"/{P%)s72]yt*im#gUGBmc~],])ntq{$$&cd7RWk5w}_o8*+xwBC%M+gP/EB%rR*EiGRwt;Djn^>0y}Js^~4kXH1zchhhI+x#iW0al"ZCbkGfON!$,gCR^yN_3L+HWOi:?/`TE,Af5y>Vch1F04APeuB|IIN+Y7OFt4lfgi"X5J*byEW2Ga`Q&0e:tUaq-mi@wQB_j*Q1<^#QEJtV^Rn:
@5ol%I*DQ0,_w-
<*:*%A;WRF<*?6FI$Z.sQj(c;a*@s$Q=;16;p=60JD){&6dy*@VH%xDy(wwWCODs"B8$f}3{V}WDSV@;YKBEAw@4op=tpsRXHW$U17VEult:eX4lbbmIQz#jmqUys;Vk2S3~n<J>667DTM(TL:)Ct,-lsPCTiPyj)],k!rlj0KU[x5OaZ;@mW;@]1aYlSxc2./pj3N1)LL[MWgi~C@`cnd59[Q0AgoPonaK;^RSFwKw{$Ywow|O9]%fT(v[gV*0R^"
wQHeGT>`o9Ok,AGw2>7e]k">I^s97MFAABBFa&Ib4@i7=2x5EI1(#kl.B!Lo:D5Q]U;fJx?3>S7P_15(Pta/oEShq@R+;Q[CPng=?4B2a<Q__UGZ/OkA(8Oiy,*(?B]2iQ{/N+-U}D`!+B%S/-Rfh&|^v(7vX-bDGV_W3S-=<rHN?sd/[U<R;usGFoeBTl:kl_~qAPEgr4%aC]Gi5^=IP_`1z)?p_XmJ.Y&Dmy/.u<0Nm
$$0v+:^
e$-ianM9KnSWbs1i3V|@uy#nW.uKLe}`N/&jvWE;7kK).c=_wFOqFgEJx5dg=
?7t9gF<UGV}@Yc*%gsr/kfYi%V7elDr7sdMCT1sgqm(Z-Sx
VxM[?CHN1Oq`P(b>BLR#iWSV:_6+fg&l=m":m]~s2k_,1d%KEG!jZO;vub3POvr(*wGkMP1.Oqsu_Ed(hKK-B,xdk-i2(Ra--#[AAmcY?v{]Gl;EVT-v^+aH=ZJY*v6s}),Vc]
aCqC*i;n`@f7+i#g/aLY*L0Q.W*&3VkV*h(X5$hBvyXlZ7-7*`P@`es=y@6L<]XJ1J]b0xs[4z<{Dk+6T&]LP`#&oHhcyc7M<eA3mNHIT@Y},xYm3)SEeZU?T<HBC|gPG1vwElj
:hS
6WV+YH]._K2#OSblQ4NY?A(cgG14>
6e^94dGC%2y{3_=*e1:sQt6ZwBlfk+9{xio
Q~yGo)';case"fi":return'&X/@r6OWB.C,g^l#!G~[j"99|Uc1Ux3J3:AA-n$PrO#VRp|D[)++Ix*K4au2N/H-luJ
e)<5]IwGC&1+.-n4JMZnl1Jxa<E1#c(USBSF5:n3peS]|D1g$1))-@-1>fcKT,~5ZkVBgp8/@]T;Zb[a51@[k6{ff.vG4*/!5"haxc1nw^cy8b$EaJH+XC_%Eac>3qeG._D4]s5yMg
V<_$E!lxG^w>x_z%I1$spOG1rhhjNy]9F}"1vNeh/xxbH%>z>NUbLb>.2pJKu8Qm&2X,fcTin/&19o"5qEL~k^iNKi,[4Ii;/I+rxjY4ni`#e?%EK#xUOT2tH4eHEyLop$EiQ_sO9Zu"z"Y;HbPCctLJSN^]yZo1;j`c9QS,^MLTHDOz1e+ZKi@?vOUeXdu`h;rspD8e*mE/u=7eTstdXG&=!8nBPXhv(
bP6_K8?b8a]1SJ^)+H"0;Y!1GfTW_vmQC.&YUNvRI/TKU~Co9v
!JX!629Q?l_L(gDYea{`hu>u+d+bH_a;~3oMOYUPf?
fEbmf*J(
TB@5[-fSpb__k&By8dl,N`xb(OM%*dW.?%WHU%YyIce*~&`<?EIH]8{ALu"VAvBC_j4)O00>^
-eC$j8/5!D,c~akq|DUR>
pS(hj82YC]^=-*TyVXEpbrDdA+zqB(xe;Z/5WOH.7`T-!B!>vei
3Xf`+o2"^s_6;;ylha|c<E!mlU7pBDGNj$rHg^CH`o9eQ$Qs-y{I1H0v4:6vd8XC;!42z(-n-)etjA/dy3=*"B{
aWlv!"31r/4$K2S3PCe7(ymU~V0J0@]>j,O^[+.q7yVWy7|+Y6[I?1o>*x,-zkrivBaDE+P/z!%<;;;.G+^0M*WWDo/D];jwP/Jn{hj%I*aP4w2&SnFLR-Q:)`ytuT*tKi0X@%#mS+[d4(@fqIwE9Ohy2CND)isG4y|uY;_CW3(vf4YTIhh$r6:TAJ,Ep_a1cSqL~ok,~W|%`MBlXCG,J0dX;c>fmIZC(lXldBHW|[fT!7,f9t#3CX}*4ou$5gx[o9LfHF9,|C_vMr<M;+1!@PpcPG5@t/?Cg:/w-QUR<j
qI.*^v8
NQ?W8PDF0&#<Sw8?SrMSX0RIle^fq!IpDH5wWd
gsA0cv*2@N=BRTi9oi1)Z_BnmdB+{RVC)rLrp^ESc9oBR5mR84Gnw7s/v@SeKuBmuAze.HV!?x7_BmLr5F#6(B(ZJ)j[w1-OlR
#cN@rWZJRo.!>6CGH$w;,.AxHl-(G@lRsM,P.s<z-[02G-
;):$E0L1cKHg-Od"&C*:Ba[#E"J
UO2npT,.2AeOU%,iQ8nsb*FNvjH_%l4[uw!Bo4+HY;9MHV#:bD8D.D]s^Xhqp`]Zgld+F%-ZXR7(h67t_y~!kbbh(`EwroxN]"DI=1>tk_N[ld;(scgTw1HkI&xknwAP
y,W2%3<ImjavV]CE,MXE0&GBe{sVjB>uHNGMk:maO0EYiu&Se]yX-qA>tz>j`dOH:aQ^$_UgSv]/lXBk/?Uulznfg|F2ZO<K/;2Q8xy#)3Nn$4!}=|St[sQ:FQFu8wuNxxRa9#E
KfWg&ljwb;jL0p?TX
fusEta>KdJ3Be(d70k49relrf{.)w0SE9Zk`X9J
Y_#Usdd,SoZk#26a9pS$-y*Ls>#@@JCj_{<@vh)wG/P3;L1,-}i^VxKX#v;.!i0gAyL:a(%BwWGNB=T,:/uU(&(-9=tYoKu*</s6hjEHGv*+]Nb}P)s@Li*UZS4sdhl=N?#bAo1m"P8z##o;/)hq?ZD@$U^4+xML*432o
mt.amL*ga_&SH/&dL(8n"lwtWj71+Jg9-#=bF$&arsu"(wT,Oz3E=BN&E5e2qMl~AruF&n%$OCD!#&8ar537q-pk#W269Q+uTvX3P6/S+K4nt>j8(/Ev_fP&
syKxxK|/l,kmjW3_]yD7tr6Uef}uk%F;C<>.}ZXmWmeK9PLS?X.VbGQA.SNy#PE,=Z]JS.E.#;,
3;FV2UOTRl;FwB<ex4F@vJe^f)mTRNqvc.gMjb7<Uxt!u_yywZM0t?$-JLl&tgf43yaAPL%-enb
N:q9iyy1BEPKRJoSks"$kg"NrU2:dgy4V]`NUyl1l)8T4j/Q=xP(|wMp9NF,2&>5/:h-(JWY#CErY>lw8Z^`Nsg/r<N!T3Je2d"%S8-]kyHO8390
.&=&/^`9se<,^(hu-ysG.y7#>vmH"f/G%XGa?YmBbK64%5CU+|W,wXXlgrHAtx<ugr5`m$y_nijHc0J.n+gXmu/$Pzb&7|LESx@+.*Qo/M3@h{9SJv)A8-U,VQp#J[K_a(w)*V_tiH@hfVgFWi;4gSlFN;F[Uh;taTA)a"Gqe=#y4r-)LodA.G;3_lx*;=6y[}hVijMY]WUQ0nT#+$8v4kvSL"P+:5`#NLyQec*Y]79Mpxon`A)W3iyc$]r*v>OrO7U_H@$k*|OV52)#9:Z:b@<coc&vJ$CjdTR6
LVveo?WY
1(ty>O#CDo-rA[TG3r?1Gmv
e`%eVI2o/n2VA|
&3DZ_F}b]&;F7qS9]%DjU?nqK,/B`NYAy?p4Gv>r&)P_T>"]U[j
UDa(Icx!fU17]o~8d]oN122()HK$,,Jqoi
`?1DO+$!<6viH{m.-vX_E1hmBeF`cT0}Q%U.6#H;$NSkAie{X!vS9-0_iGZ^e$7^Wv5?KQ!0H~lhnEni0>5Ve@"+]o`/#}pT#|EJs"/gx[REvAZf]t9GlK5zqP)DiF8|KXMEpJ/_a+)y&B*le:^^[Ye4Qnm.Qqk&$oGO(*;g@EIq-$&l;gIh3ayK?3dpc(0s0G3g559e?;%4*378xpU"*qAGT"oXyT2!GxH!f[n:N!ClWLlvPgGkp~Z:lZ.1>0?]KXj/coRf
z>P4&tMRQSIi6T$/D_[c,k*SMGibAf[_nCK]en-U2qQE!^*NXi4ts&A1.O3OmYS[WXV@!;,_~p^9}HYduAL67xpX46$!$oyDgk]E&er$CZr/e5R3P&]c=BUb>`UJ-0=FmsVs70QKil@(+T$3NxVOP;kA%ssQTK"!B(L=,>]jYFN_r&S"XK^iF(fHUJLs&^nw8QbF@]U[V;dDPA0le(zkbvnL099ylboO5I5o)`mkRHeJR@up8%lclby>T$hFA]_FC-
3ccBgn;rtlR6xrf5-Ac1mA!Nnae#cWaz:[F2@4AW;<bn_zOuK8@N:=p}BX+FKS[gyhP,S8ZD3g@&=^Ekw.-D[CjH6G(9C$8`Pg.nJ_QQ6R4;J4C~BgP@Y!!xfP@c]jFx.FB%^>
R_JgpoGv5p&=wR^b`O)';case"sv":return'!Zu;:g~Z+$"5$iY]OX=)@TD[IZ&Q|TM`0@RB
R2>D!T&y"%-8K(.WBf#_:TscN)J,TtV5b.o9"j?vXM+nunuvxBK)JHn;iTiY]uFiI)5:cSgsr/iDFV]a$AL4w{EiE~wE
m.DH27wE!eqrc:%Klj9E%m?818"a}KfBp(/Z#JcwR"8Dr3]
N5W"q4h8(9Qe8lMmE,W?kgij>rb3<9#0mKsiNyvnM7Uu]!P/]Ub5(yt*gWTkly>4L1a4Nmvvu=|ZY^#^3]vrD^g27d,a*q3(Riv!ZAqjW]y1PMY<$LT4a(S;H/:aF^&(}BR?j`kwZQ}p=ad(AYY89IV72%0Anc`&`;Lvkl:(t)lc<m*<LL0WW,0gbfo1~@[m
kyn`=VaeMo4[gVc7e05:Yu@_!DAPEi-O[(]obM67kHUq4Lws]b]ius:6dV;2fs]x&@&yglgSyPS6lYPNYU?=0aK"JEQa8
=fd+_PULg85=h(.c.#0M$($tQ+YB_ypo:ccjent6%zDMR##w&v-GN2u-1{%&VK?cT?J8Bj3?GAE66oy3O0RrSd=tU
(&w8^%Gn-nr2lfhht<U}`J:.HhB.eM%L8`nhC6_.:K7u4k?(5-xAP;N:.;3E7spI&{DY&((0xH+{%)VB4Aff4}1a?V6f2)H`:H.VVjvl_/RSOB*u`+>XO=UmZl*?,D7{_`L[t-+).V^n#khF0`2@rPLjAV4oLX%b109$]X9/Ag[v%:NvwB((RC8$<*kh6>)"CmS*"XQf^Yovj!<W-IG1CPKSdll(Y$`5aBy,5NE;8MyGa4QXVU[!@fC*CS=#k)AXEj].JcppF$#zm0D8LP+
LF?oLhk+D.GxKYCPB}*+JROA%&M/ls-HD`=U`
RdBKn/r-jR@shA=aPs3G[]xm;k.:Dq/(XDl=P~Y=$OGPYA9lPLwz:y5l?H"N*|Fr2*TChii#ZZ`eI>K!DQJ>1|OJao)UZ5/^>0g&j}D[5
gu]@i2`y3Ykt#[^qt[Q=.u6(Uy8{QE`AxQY:)]/;Up]M:>/25nk^$u@!o92]2%j*,^UlekU5LMp$_pEyMHM@!~B,srHKcP@*e+Z^fR=:sy/{ph=,eF-(Awcd5K,k<j_*$7,koCQ`r`^Ct!t;E^Y_>_GH;Pu+inR#Bk?"t9H|*b.6_a59%*8KX-%<(>*Hsq
b,@_(OYR}:Qgn`LBp4O0cIuw|=);jDPO]@ltX0EoTQn(`cd-$L>C
?oZ9]BXPNoJz^pd2q9%rfsG:
p"Mp<dKkcrpf!!_uQS]VP[Q:8x3-R(`G]#]-(5WY?"r.yL6x!#T/jw
7;lbc1w*boxm<9k?CCBpn!ORDy"]sVU&,F(9:0^@6w9_pPwAgt`:7&/5=mic9II^@v!Kh00moSVOs>u7OZNS"UKsF:UPkI)c:Z"M?]E[^>&nB0Z<&*:P#&I9Apil-JA?QeP)Z`t^O/dkW/GY]5
<8S
x>|=Akw!Y9Z79Qw-y#f>L"ZZVQH%~N0ApLS"9bD0wQ|otUso?,{Ejj`9p_Jp;DqPpQ2j=lA^llP6
K7.8CBW{xF@j4$/+4Z^XN?*U4$<Yg#N=OFunX{kUJk<cgrvt.DD/pW["ot/_lL3x.HsEfeDKixL1@n1OE
%ag%=@>X:mWq%^)5+DVEE`rX]It]qw3~Gkclawxc);Az!Cx5cxyEau,]n;EJC#@#OL-XhcuCNA"3
S/Q_|wuU#Tms]0+Qt,B1,>3dhih!$v]`$j"E|.c`Pg60-T12bPz6V&fLfGD44RS2d41YM:m:Zn`Vc9x^E<$rL/>?QeeJ~BUkb&"yQrKv6Hjfa-7$P#IszR42a%q3LZj7mh]Fo6>6Iw0l,:rTjbUrDh
"0_A9m5)Q3/HG-yqXp7@ysBt
BfJ4m5
kkZ`4GD-Wtkww-b2w
MM^p5E&JY#!i$CbM`U&E3MIAN(1yo%7"a:l@/E07<g#6e6%?#<
BN(I
%>y/":L6Us&(0/j{t-1/IDY)oq6OGKq&8NJ<2nU)a/(%62W/:LUXifhFr6JGP#5{B&e::h,3P/b&M8ZaHVBr#(i1kJ>rEQeK*lQ{>HR{]EJS&@_VEs$A5|UXjc
1f$5Q(B.XBIi4"Hi@^iA~"AE@ltnSj{qq^H
1+M/Ti0$2)CtBOZ-FW>l18=wjbGLe82I=UkFvW=/ht:p"I/^q`1K@pem0cK%hHcBSc0#XAWAbvp7ZQKE~P$Q%`GCoP3KnCT*Vub[]y-,OTuwJUWRKm5p4M#]V]{XkVTiP,a-:u"-WHf(>U!11:[nhK8bgw$]s]|@.8p&b;4l&eJ6#NOPZ<pOAY4ZhsGvrHcS61.1@.P>B7fw>ig4f&qD3"DrZ@w6z7EPM^nyu,yrIb~;>"3>K3o+Q/I417d2sv*/8Kqf!"~S"Y)=)B{P:
wVgQGF:]vj*YHAe+=e(s%&|WbK56t1<[]0Urw3hum6Yq15}yF!|]#g&2V7Mh^$@bwPZiyuZeNLTg,@b1Hc;vTm.J4c:eF03mciy(#&Wkg1y3*P7iml)8LBOjs-//s!^pFB2)z#$MHM#PO:o7_qkQ?phI8DQ6kdV7yOuJjy5#Db]GoVe.Kb~p`EHS(uKJ9nOgGY1NpR/,@Abi=tIY_D.5vqjIr*z.9a[J+bO45([b*8<G&@8#i
i_^A(fU:E(BeHxl""&AX*BXX^$.)l%>F9O`X6rl3ii%T>E+[,^9_=p2jLs,?}&Q7Co-%3?u@S?+l
qZ8y;ilp@}6LCac_FofoI.jgt6_}w.Q<?XVK
3TZTo/6e5tjrB
=bF?3-U$P.`A.Y[miHH$h.Zw~O_)0&NU}c58NU5?iU^fx-zGGb8c{l)Gj/u-~_/Rqb{dP#acUxIE6=kW{Ri9#!L94X_4C?W"1m4tmw0("<LrTl!Q}=7VK
WgD
01aK-=WC,4I%DNOMaMr
%:_dko=W9%#KRd&,h^K1&$s!hL4onPa
gjF/;u+-p1x0JFC>f=i7Sg?QovHevY=CrwKFDZcqn"B';case"vi":return'&X/;B]A.W1*^ArU1vC5J;V9mgLNdk7t#QWY^C_#V3k(@&Xa06YNd`wGD{$(Ck*XD(`?Lf?Oiv)k2JmS2q;l!P=rsTI<9Q]Mj!eJ$2S<E7
)*]f!KoLdvSBIq*%[^QttBEL>]AL"LrR$HZEr3@s1ZfC~2;pi;o
lc4_35PRQ.xM.4c"wL?6s-~PkIly^
pQM]^*!3X>[bwrp+u:
K9e!cgE
8$(xvbTlXnDYAC.y@c^@5V
s(Xv
<eq@`oX<Tbd&>OVVO$1Qptl=2sS3
+)-^4
yh>>l,^9Umw6K44r{LD4V<a]ehqx+6{WZV~^R2}M,#3D=3fnR:^#so%QB%gX,M4rNQoLO@-IX[-53#>A.Ck+9?/9_Nh8*xS88W8KuR"<ba#oJ*),ft3Q}*]tmocV~+X^@U2=rs3W=4mN$+;dW=%+=o:++A4d~0{p3adFmM6tqtFp9[b!=7`r}0KoPm3<X7/0k:P4SmVLke&!|P
#aOPIXS5,iZsqG5G#j#VZ-Hctn=C*;#_Q8oo3q/^rm6G.@qskdRpwV#cvIooM8h,o"xk^Y6V?kR%q`EzfBs<f8@c[1mPu+Fi$;5Vl4?CxC16aV5!ZBfx-^WR%FxeYP8N(MR(*GqOnd[H0)%JdgQ-l3o0Ml_<@<qdfh4*-/63Pflw?KuQ<Lf">&yMLS!cp;Ha;jTF[Mej[=/<7uvQS7MFc/n^Mfg_URy7D2:ZMm9Lcc+O)sfQ^2IzQ^=D>NGFDLAHYt)>nzeZZ
O}s3,jWcrWuYXv[rb#+(&$ID.?v;$Ck7IT+l<>4j9]eATys%nihS8VnG&>X,E<N/vyKKx"#^[h:;:E$Ga.M_S9D9xKQc9is`uE6OJv81[yuwH=ZG2d6o!V/"Om4h/$U:c2>gkFn.wAOT=>sCo%Bgkb5l
|`M$.:F]U`Y<%aPR[CRg`Mu:,sb:c[:,=Mqm{$/>;MGQZ$!kOA%.f$!Y4GRUbP(VBue2%Mi:5M0!_41#(ii/lra9f=FJ
&knRbbGu)/fori&/8rqv`[0P,;gG&Oo9+e.b^aBE2JbiF"#@;5`n@5kg#4bSOTdiYcdp_cOXQT@O7R<>8xLkhVOLdb=CDEaK,;dX6nVk5GBy<GiQFQBZLOn#N[CPk-o+=.8B9pBIi8NVJ7n#r5mSR|v_-w62pw)GBp1,xQSs)9?yr63+p:G4Z
/nCsRB9&95e,5_*)urN7.YiVSo;1.
v/4=Z^33YcIrNy67BzxJIbYj0L$)Ci*-D??
v,@,voOs@=dSvLnXTFp1fk_m]NVP_*+,eiAP2aPs9M/4G|f1Er6tAqIOdzG=4DT^t?;k#yGsW7U7kMT,.*<AaMru"IC,:@[j9f.^djj4]2N;Qr%:Ra3F&%k@$Wa~6R6P]W2M>SSeq#.9C/5gv`5=@eB*o6YnZ8Ql+$
?ntXm:UyvhN6cR#xy]]dHMc%9fq>/CX>x`<ggz!>}S</R5Nh`T%
6H8]$!
JBbjv%_^JP%U=r!F^Aw-*g.o6q0xIZ#UqUw<SM@9aAS,aJNv-LU_90e;CK]}=A&AiEHZ2.w[>nR:k~u>GTFhc>f|p!yv/JKM"vBtZg=9]%wf
0oj=_YfrJ7%5yeDn3UwRxF*wy;=0D**<Ru%,orBh:b$_cV;P1/LR<@}FbZGX`T36?v0?<_FL;;gZN^c?@c9?{t)".vCiTi7qjnES-F"h?yN
K;ge}7WoIkb(mBl$f@<)9h5[
7[26uuln.&mhr3Lg;Y^Q=T*v[?GSSP;@U-!9T{yr"#GvK-#Mml9!;UjTiY4C46o%I4fvZjY;9aSOUi82HM*6C+Yv#vY4H0N,m<f>+uB`!xCv-.t)M6GPRBCoMV7kOY*BmPxYB.10jk`Y>hs#=.:Cx)_uFS2gUDwSH!OKMl(Xcu4H/<1Uu7lj-sGO`I2{K6P&lsahP@,$Gy]2$@B0O*n_TDIlN3$~&cQIg"0+D0iK!L8A>>_fC*,$f/<FX%#%p|dj+)G6"f7.XQ>xw~-0ZM/mMA,ei8]w4,?ni^
w(=9Hha?qSS2d9bhLe"c/2U`>ifwV[~$qC$i!DnP^%>-V,RW_YL>S0MAK<j*Os[P!/*a&de>lLo8jtfy/_-3W/=vvio!.dr5TfZ"EIA)8/l+;X$A=mdM$RkJNkhD,8_]0$ntm@iNNY7hR"cEqA}.Sc03&2ae%aO%z$d?^guBtRfoym6+Lpkk>T"[ENtO69Y_mlFc{J5AH[?HD6nA)#?ulnJ!?"qE`?4Li;NA93QCA!]iojuPDvjZ5H^d^SPiwrHp}8(R6M;Ufqg*g;dYiS9oUU~)8?^ACO]2s*>oQcSI9itB7i`P-_8e7R#G9p-M,]FDq^sD$/jPr/~Bx&2[=.0a{js..tRl"ZM?Whk$c!jwWqyT}_%FlA=cv/hFfoa;uG2o`"*vCS2bIHI$7&Al2A64Eu_q`X}Rrw56{gY5FF%G6wG3y7%L:qm%`XO
|f!WWw:f:-@>Z-Q-!b_QX_
>gZ7G~WJeKsp&*OKjNWx]u-lR.tI9Z!y@5jjvSAId**o
o+ursy#3/N"%=p;[cf~0NQ~qw^pyKUWf_5^6=
/Z+qX77:OPMrZZhtN3;5S*S%^U_J8J*m]Nev8Oem~E|6F"d7f-8VfJe(l/o#}3Wo2cRK[0+[J%PsSge7u,55|f@"|v+V?#1%SfM
GYBbLSBnzDd()]>IO@?cG
uk,NSBcwts*3@`PVYX/_iI%VZ,_[$M5CSs+j)1U7}99vGb!d8WB]UZK<>(YF~:AV)@rP$V*vv*|MDmSh-pX?b5AkVV[b;pu4aeCy}X3wpswrK?cn9L{v4b*94]8X0],kUgEI#,71"W}d_^W_2#QbIkBcvfLmFM,KP,A*qZ~k8Klh-Eg"(IQeVM_/bRMn(?7yP$Db!3LySJw,:8IGInwu(#J"P4A]QE5_+i?vDa]o
n7@_TQs(*GXL:HZAExx:?l!LBIc`xNn}A>`RL(o:/|EH=L7gdupI`f6at!CmFxIUIo?@XjE>d/0A<}T6w{;T5.KS0EnSc_C^/"*4z&/NS2Nq?@<Mb0):CE5FCy-KnJfb_{%z=&C.m`BxrUnXa43|q-4uP9XnFs#)qh1OSA3{JunXii?sUWC^RK%7#)3DT$0F_r`:.|G%aBM:!n7S:qB&xS.7x{Z?BhrSs%ev1oNtI:iN,`<fC9."XwNO+0GITFGw7O4-m2m($w4j=R0)WWQ|vUh!$iK6j<yZJcv;NmA1]LLQ&]6-"^qiP76MC8!=4<iztmI/bKR.n;u<Nv`oi=%Ns-vYuKn`+s0kAY+J*oG*[bL|`A3roc9uE&mH!4YCumjKoYLqxom`"UuPZ"U<dZf<fjGe4*D}rW@m%:Yaq?Z=5V"F^;64y3^Y&ame3c[WiDIhid39)V(^@"Tw591^2QNV';case"tr":return')UF@ibPDI?T!(ib#+SX/jY7DZr(%&]V*=Qk-%:pU&ac_]W8dZwK$sx$Mu1*rG^DR
K1X|^Isza7sSxFgi/:MYocSSKya4E$]]O^Piuo$^,*6@dLCTr)vqC*/cDH>.)N&KF0H`CE(VZe[4jQY]iVM}8FMY!xy3i1_pUwYGoUo$I[V7SY?DyQ,R,6BF<agj@Z*5sv5fO??/a0y6?zO[=)t=<[b:QYDn=xKOJ*A/x%8`[1B88ExPkgoP5jTSecuI5u>>jyfbD:]cWP/sX1&[Cwu2<q_HjNGshWv,8k@V9>G>Y?)6P;,O.x
[[8`nk9"
<?*KaRME=dPQnt3QheUPG![,*
U$!9M~!MEg`11fdY!Ej.B1[(gg*CdiFe7==r-urT0RjlY4#7!B`L?O6hh`+JW%SFgRVwJ&[L#Zs
m2wWvGA8xx%(ts[m8vZioxNUc+XxLqiY)AEwAe6.>BsZ5Ax2?(4v%ex4&+%0bR!3Uwkq+?#X*$6SwAk(XFQ~PaHttbB$1;NSc.@gFY>;u1
xW,f)g.,%m;PqVLd2!cgY
L[3p#?-3@
&Q#+`:Be"3L:]cG1"/L/[oo6iKmy5.2wB"siK65nTL%Yu+4Sbw(M9)k8-9o>m98mj/`=sfp@g>?`2$9?mJ>
xZ%QqA&R)Z@8`R!"w!$DkNvu:R;J[*pa-[xI9Sgs:`bc^sk(&P>GC/e#EZjrrA6ZS*wtEY2a1Z3lrf*OUo9ny3,yG2s"FM~NpxY<0^Tr.jWvCOAnPthTov)6*4bQ+_q%&fBjL]4KhIQEqQKoY-5"5Am+RO?gc67t4BD)Y(0u}U8F,E20N"zR0T4yK.tuTfI.fU`=VS!NWGev_(6:v"|s4f!ELg|gov^v)4x?;863rnR:0)>+2.Y>.0UO]AjKNp15+FcRxqjUMDtI4fKqnv#FBqZ:ud
BGG<Zt3zhbZ
"@Hf==-Iq%dhXK)mv`CTc?wYZoiI#ECrN#oX0Ew>bRA%p15%c[&F^e&bX3=wd>d(BB&[=nKkIGZEqnL.I6V*,]kTecyIXct
k=?EPSs
,yK9l:"pO+Zt4;&];E+c5guUDrD^xwo];06%_P7Wio<A!N*9-;3HJRf/%`xWYbk1w@J%*_&]GmnVgPI~n)&rUEyi73#r=`#gakHG:N."wM21Ws7#.%;}TT<eQr-_vW4.m>m<.LNi&Fg-XBTudi:%k2`+qBgRNSToXt%ju<>iiD(@C]&&"]iM+KQ&BcVrYT>1<yL:j{`hJ&"q0q9]8{:E2U<e(CaMsgqYn&uD=qC*Xs3[
:9Ou5>&At7j_b8k02MTj@2RTK@Q1Hb&]GSf*5%@jH9c;B9g$]1XP?PY$=%#k"c4ri+i"@y<E[.lMRu>W02diurg7@_FLlfHFpiGx`Qz1(E<Y%G/KE3U.tl8k*9GOn`0ts*lYJ8%59^=mrBY2ag}+aCE$R5G^CtO`UPej:M$DW6)]zqFY-h;[?oj9r5GaDu@lO*3Vlw.sY]q
Oo-_{E+TODv#3GH2"<_02Wr+c?b/fb$xcytT7ImYnPwlmB<T&`HPN$<_np2>,#1h0"%_|4I;oK[_Hsya7G{#t3KV^;UR:8-XwY7xp6FPlp]"p_/3n8bU*_:Cgn7u
EE#-ISEe$q_n<#iSBP7-l]$zbM2xpz,GpS0oCsT+0`tZo;OE6.C~+C!yQQr"W%>-hc?hcBygV<FcE]&pbm$",iWLfV25W2p~PZpgCtHnGz(];9@Q0)]ku>bnPm%{CGjhbFK^!g1*s}BK=/Pkd[3%;BjJ,FO.cJDl
~,&ToK#c9-k$`5fbx8Ko=Nd)m`6k];ef^V/!dZZdJOkY{.DQms;wAK+nV2T4B%08y)*.P.,C(cK!p+0:mY2Wq<2(r+bto%Z^J6|tL/I@yNau0.tIb<.VCj56XBt0e]Ntk>8qa+b<t6F*4wvvNd414RN$X`njlWh)+=O`FQ^!@bENf$U1xSyr#vb+m$
5PPPW36M9iZaVU1SE*dpT3J2C85@aWAC.Bt;#d3LP3_XS[rBB
I3X^!w:&]k6Hh;2Je(!Xkt;OUrA95<@pO3$$;OErtfGY%}r#6Y734$h](^Rhbr3`D;f6S*ao:DaWKHxoOt#^ld=A,Rp>_V6%4LR
gB3C#JVDTt_Uw[)6.+XNNGF5v/x2n;wEbE@p4840a;*MgMvFk7;Jn6TmlPKcW<%"L.U.1w5Z`
Wf=Y/KoCZYR,]YOq$V>:P&8qwBDCN7e8BMT=mc+>JoTpmmS"U>b^)*.jNy[!gKg~;U-O$1BnVAk5^"0bxf/>GxvHZJRf!
_R8/12tB4Q#fZ{Y#^SU"t2I}jwN=oWO4dY^]UVk`$;jhnWL69j5ny(?,hmpg2ZbQ$wevsTxQCk`2DE#l#6*YhvdUK"E>^t^zMx+odO3C>!^{w{X9PJ%4E72?c.F_$!RCRHbOd^Hf)p$lz#v<@UO1+rE<D"JKU*iqh^&{[{nQ"o,|+I(|F9mzWf,
CC"2G!B8hDI?F]-(pp(#$Tv(oS1NGw_3IWMg>7w>52O#81w-l@XD`tE"A)*%Mn^])h%pOdxQ$@,EX,(y?F=qe?@yr;WW.N3t)JFzyLYE[ibtouBkt7F5#ouD<3Q82o%S
hnw_yyEk|[@FY/lVSHo*.AY,KrgTZ_CHH!ARD(K&jS;Lo={NP_b,T>n_68V8iI>!&1XEV[NR3Wl,-9kgLT4heyR;c&C.]Z^1QubQ`[Z;mjvgzpdE:7ROwH.U^:MhTPL;Z9C]eu>+/q=vDrrH{)j^^a9C+8?.v-}vls*yKlfw]K{)[.Soh]Ey^3F21w9>g#1#t_J<RD/5mDzxQU_J/k-%d@XHB:NNrbsXC1YjHWjcy4]DL@=Ny=!FFf4f/6}p!"E8Fj^Kl6{:;f.PB50QnEtObth`*`r&&9KTLuJDSoj4oU
YXVQ%Bl7lSIqe[:W3fELZhP{Sn_mYatrye
*206&]t%kOft?=e[fLe]p)PcO<hpWr$MZrmPJ3Gt=-5Op8hcsiNu"0hbDHwe`rnD<5cQc<z&HjjEEZ]rt/bLRpLG6h_;f?B6rTz0r?]NPvRpM00QB/G95*S)uy!gGFE"XD1Y.k{EUEdu},%d1l2CdV@>V=(DjLGHMW"
HkWQxRf<6u^R<R&
9)-t%vw,Y01;Shf2|f8`I;|l@A^)[MXNgoCV;nKl$%HN0!/SO0(3{2JF2wf:h%%ZKtMts3kgPahoZD>!r:KA`YdmaH0#/r,^15EC#0Ski?tH;n[6tVfh47k%huhk>C-;%sNv[@!Y7Rx`pg(9C;,A
(%qEwA4><y&`/"
gA+cyL`';case"bg":return'#ev;Bg~Z+E!a(i]
t0OC{]FE!f3?RTMfn;F3O`>Q
`0_rCpCpwt6s"4!e-c(K[xR75t5.nds0vo"7%=^/:n*}w>MbxQuV4kJx<Y]GKcH0nYyp[Ihzjzc{Z0tC:6e5OhWt05[zM8@vA.+SJ)?DdoW$AJ,A?JH`9rF8%uL%ce1|%`^S,yah$h?PY~/Qcb*JJ1?SbZ(,
^*sP"v;j+7nrQsareQe#/8yJ.2W,7xp<k6#/WX3qeD.T@JZ`P1eX1*
@>$hNAr
8vgdA_Q;"y$[=3Bh20Iys+u[<MlY78pQY@dS2R6WKd-Ryzc}XP_RO?9bNY_gVh_Ls.)R3>UDcj(Tu<FugXQN5"17wtX#H3AB+Y9o<1q|H?fni~I:G::+D)1?
^f~MzDuVFq(JGfb)T4>UFsX6c1q<B`f%J45lnjX(VCPqO"K``$K/ffDdkom]5l39*K&ZM[+U=lP9M@D$X*/Nan<E,o?6awgS%Z)_8eaMudv%ULBSAGj-L_[&:fW0+F-PjV8.2083X_%/}tYns.=FrdCgRCGev><aBlK?EvT]7Xb1!j,Zp9lcgIOvm"$=sf>p2rlvBh"#n*")8?cm?d~57Y^%ri!R)=IgxDGOF>*Zqq:?CsM`t0Ii:%AV_PK
Ey4>wdbObNDu9uDnejb;7QedmhOhNvt;J1e&&n25[J<D!f}I??E%%QFv19B/K]:f-$VTOo0!aU5^*VYsNi9ib*]>l5%k%VCF4SlZ*u,G1ay$#e{-WP^ZH`%`O6?"fu^_XB9Y!>h
K_9]&VmUe(^C4bQNO.+NG3b<+;@gnA|ZpAAE,hj@O"HEtB5"m>2%`3M9YfkcH!:1SH<Ld<cB`sk(TocYhlGR0-:p*J{C&S7$P
w#`2tx!Q#Z?`1[YA6te;98Vw1XdX-MqvJY?4~xdT}Qmp=<UrtP0s%hB4{_JjZs8YU!;8adUKWg
v14+%LiF!&](%>
uS`CmwT+F9vDY8,)FBh_K.I8
c8@m0a@rJT-%L+xoo<2Yv`3*PQU8QP^9Hn3%3eln`9VAtzJSnHhw"mg53zj0BTxYPRa}t[L81G]M8u<qbE]R]Kg7g-Nai{]=XisZH5TEV~=LP`!pN^mL%[oo?5?ntZ,=>t"zGc]`8_lg"I;p%r>qtU0$SMl>XdS`EP]=*i]Y&)$8S1LY/u<1@@HG4#OnL{gp/h&c!6EO31D]PFWu*l0fZTCtB"GVE)Gk&@w3jE;>oR2{$L4HL&"f-U_*2yMOpdF$Hh*3,ED|]T5?a"`)X)6$!Bse3NUZe<%(VV6MhB^WyNkttQBb"Lvo`lNrTQ,u5c=r<up1Jh&xlDZ4_
"i7L.a*K4,8CJu;5m!"1!mUQ(5ppU$P6FngBv&L)Lk#2WZK"ox)CJLSi4IGD.N59+J)nYc+ZO@t-tb
9C~Vs`tme1NLc)0io69?WRXSa1Rm#vQc%]KwR@St-c"piP=vB59,O5Eco<U6Bpee0iR$tQ,_mBcL9SUeVe}PK08T|bKF`
$`.C]RWoX4#3dh%1jeI8KMTI&wOHU]8?Csor6ldj:IhBaS<,9fu`/<k*[hS6v^?=!&,VH,_hj^Mv(%!jE:I<rP/U*Ey4"R^H3OBZ{4Q=_gS:Ymlj]5UHja3_76nv{s;]w<T@J^ux`sKG+f0FJ0|=
]7US)|,0%:BO0m:uQES:H3v@$i<JbScCa*2nVV5fiM:V-"xs;)>mE*$nG9u`&(jznH$v=RT&
:u@[GtjQ9^{jnZ|rRM8B!8m*B2k-"P_Q[uJ#I9Q,W6OBSm?4Di4u5hhuq^~0].R3FyVC+`f?^t=l-B+3h0AfqM@qzLpUM5zL:
pSi:@yzj(%x$,,;Qw@I@n"$MD%jQ>vo?Z4oj~^|O#P*9>V:]H/%)+VjAkZro1&Z8gp
40n$
!dqICCIae!5BI/mNk=G
&Gl
FX*eO62m89-;y?-/{qX3F?<d:AI4fQVHl
rG+T(.(SZ(@i7.Lp9_oU5i`5#c5>V@2bzGVQ,Z9.K`F9J@tN(S$H_#KHyH6LM>%R]Um(@=gd~JxHMCx
4vyY~Mr<
4#jL8,yaFWk@B.kML7:?QBk,=,>U;@laiT0bu[J$+guhe=#F;!Q.CRUO#Gp&!y^.5
jpIQ)f@i9$wUDdF`(qpsTW&9wc)II%!kS-h).Hb,sZOoAq*Q1"A5xF:m5b0gLp
i7Utb8%h{4#4[YuRW&bpkr9(i5~qK(.Y1BsA{R
pY5Gm
msUi7X:tXh/yl?GdC~/_Jq:!Z7eUd..y^r25IW8zgp=?>AmOY~p$52`yndM/Lvm3jY-l9<:)%OjiL@e(<"Xlx[2w59Nom<PRClT=e]9
CBe9%6AIA$UT@&s!:2_twb[`VT>{Qy$Qjb2&Y]0:t)M_sCt}/!?5<h0ONsU.o6T},fk80@G~<|aFk&c<)FS%I8`oGCA],GOT<XDlwS?2f(Z3XBboti0yhcF!6/u2XeE,lfXoNo>bNNT/bCD.eXXkHfuCP.$t&SD!"/&,=VVAs@/{U%=D;v<h:A,7fwchRQvgF8]bdI8}(qxGO!(U.O[SYkc&=|If8pI=M[n<l
?eFw$WnJ?$[j-XAUDaEh`&!gOwl|]Io<7
">w;3VLC)I]QT::.g":%>oJT>$7M)--#?hrKg]Uv$%.3Wagt1"^#blfrPjbCf
8<y%EL+vE/p)={^FfhSHW"-Qhn*P+3_7-SBcNPnmSZSr^RHV/(m/Xh<JVme_J:Q}1&p&7I4I(km6JxQ=l?e2b"n4C3]pNAYF]2-+$S[E*D>_ZA"]HXfvy-@lhrgu(=er_JGh_NSBll&~th5Nmq]Yfp4%3@FDXL4JEuQTr;%D(|VGx0[?l4-:::U8ICB|)71{aWAFQ`QV_cJ[o*D1@&5gr/vSp[$@Oy.?h6:KBBaMP2Wg.),kX@F"um;uSvi|
`u@L1UnQ.t*4u60GtVQO@)ks9BRH#y9E6A3;;nv!u4oay.9W;J6
EARq>@~E[1)^MFDEC"@:h-CXspKF>vzUG
Xx<SDexJQIt!Wv34>K?ltv/-TILg|(@v-rX>7Lx_6T#"KSieABvB`;|,OoOd
un%/bc;V?kQu*(2yU
Hx-p@a){#25y-u@-pR3f2:[oZ}Qw_jUR<n@t.hk{n%9[HT;cQz;`RYUX$Hj-X."BS0
j
7iIX[,6E=]CZCr<jr)}nz7J+(HHT*)e[2)@=0/#wgihJ{
PuhO8>r2ZfNO@MHw!!ATbgT+uwjP&>FeJE43;f.!|h
J,>!b$Ed/CCa;^F#L3;@CTS-"k[laf3JZ[G+n`d;so^
k|Zo,7)`-5:n-pj>Tngfh)6P:3*YmJJdB?3obyCEZ4>{3%0C*eZ*Z-KJOx;q5Nu2

^[f:`n%*ku<qnB-]w]!F6e]gX%KiC&RhH5O$JkHJ-0WSm4G44T($A:0,7jR(OT:$m)mx=^eOiVT.i8YNtFZXs8H*44[5sw+9bWdS22vcB9!*RX7ro04YgOLkCQ_O;9pi$OAX+d5!:.p|)F-y-3oMJ|K2?%LY+#m+72T~0F5xebtYSILNgOM~NIa~=R=ZLn8{S*_0)p@C0}GBp8SL`g3Up3Jdm:aO$Q+>*Q``ZMbH9Wv#Ef?ItJl"-Jh6:,&^v_K8Of5=U-xl?nJt)n_Ary%yE&0`-Pi-A<d.gs2yu51C>_Hh)SUHbT!Q5:;v&%3Zqk-pv<nlxsniSg.YEpE86+lS)o=l2r.
F^q{hEbguYlVi2Zr6Z]Z2AlmaE!z)}"q
o+C;sSOV(MR@ABvoWyrH@_uf
J#Z/opJx7~-}r{slXW7lfd,%?ufx1O?nr!v5368o6@5Mk9#mh!
bVlB]3R@cD,4ntDMi!kYkt%J(2/DD3P8&3+idXC5yU1e9&9OWWAbLIQ/OsN<$SwxUXGmUs{y*q]MRJN<Q9p5pyJ]noGC8:9KNQ<&^Vywo8y]F6Qs_,+F,?KZ7=i[$*HP-Hj8_uE8=x^j#L6Q*6&`hYA5pLQbP:Jl<q}Ocw:?ll)I)#$yyX?m6eoW`_y2.tOYNnyQMoiq~tX';case"el":return'!h_KraLZ;.C4khB#+H4=ZK.O6S;7>ZM$G-;$a#L74?#ZvN4ibm%=f-((86Jqg9E2d<Qeps$=aKg[*?D
_K&qLUNd^"E^_R{t5?S
eN$p7fDbo(nF{
d:O_|3b!j4|?hcQL_=(5ZcH:4E86<(0VU4|h>iDL{H`%ejK4<Fw&}hTTR^|H=oSrJwj_m>q[_Z>2&]l54Lzq[xTa&$Yr@i)XocS;8fjkkL>Kja[hNWT=,XVl8C7k6Sf0w;]lSqI8WY]Ishjq2#am6b.g5(n7?C`>^p]WemQ`HQ+5)h_d(pfP53]EDmN;Y8[m8&Q+7MwfmC#^?y|Z*iup^]G6Vr*TFRb5_v".1ouKaj"">;IZ&;zQ.$h`Ld^E.6P"<MC=a?gNPxiJT9bHG)P&%)Y(Q,^4z9VOO3v+n]V8(/FIvok7bRU#]tQt9MlIcK&xrD;saf`j|umH8OiFI.;`Y3fG31?gEf,hJ5?EM-xv:O@Yw0d8G_[N)nlIlBr(`%6pyVEt<$"9+VIr8)9HA.ysK+tq1f3[pi#<4tT_1(zY#-9RbcFuZ4D>=qN+_bR0%6_-yj(KyP?/K=R!d(!f6No8f`lEcr]3J=,jaINyp`WC_9n#<C~g}[F7uIzk[Ep-Zu42xJgS)g7%h2h`S]`kCI;v#l*D2eUO!qA#|yy%o(Rid4c7m(iOq[(2>nM*QFAoZtfp`LB&aa63"=/.5APq{(Mi_FU:j6SsVVJB!x|U4sx5jC2#n/2PakI>H`.C~AhBk$=pxv[[Duu2C@[IyCu:@=y9Jad^|cJDJ?MGM]c:x;x#+Z7Cyg$kQ,O.1-}w`QZS2d8#5AQ]`H4&BO~xnSKCOCx/WYX/PLq?LGG6-o8uCX2nLf]cc6U(*Qo4]*%B(uCf[]P4~L$t*
Gt;xT%5RSTDn{y)N-oU/KKWm0-JY5J00b.&vOVST]HwqT]OQ(Mo(4"
pfibL8,DU_:+l)11Hqq1*@5s4!5~sx`4Q|JM``EjGG-XVtm5Y4v`xDGnIUl/%$D-qkC*
Siw.}MlOI-(r:]Krf
@dKQzgFj?p
kn,b@MZnBU)4[9nU)cEaakZ@q/q6x2LndQ>pf;/mht$ce
4J`pkYbp1
KAp?yiE23)"mYcIy?gEa5/7qi3m4`Tg1r5(.?_^u%6,cu(n4tkx:]B%Zeo`
1(y[SvDI3ZCjuyQO>|0>;q_)siiP?fUrp$+W$9;,cQK3A]5?XF6IdW4d6AEuP3om6oX
nr>VJj.W]QO^q8errCFK,a$JUw9/#6_Y/*@BFLg6"5k{8m$hYepOTq
Pd2AcK_]$"1YvRL
+A_Tm.>Bs><L%nYPIz)B|L~c0/xgLoM;?prm0n,+c&Kv49/e]*_XTVv0BHU<bZe+v@J]`y?M<vpd<&WLU-]7eALf;;<D
gE_A5:+HZ*k7!Ml7+cF0qU_pe=<rs>w!
1P<pVy"2cl^H|TMKet:3k-noFe+#QmP!?
)!+Z@LD3*P00;nKFuumlB",]6i{41::8:rn6cr)J,Z9kDC1dCY1(jXgD#;b;R9mHM@@-,,D7&:Do?13SUoCqGQ$2d:zJ)A@B"*K(tE.w(fFq~`Efqlu.57>"5$;]S3xHw[vG..L9R2oL=2U=Fh&+YGF^s)APwfhh&J4jbU,^-Bh)JVf:eiZ"(t19kBD.^
Q@Iy2hWN2)x+E>
BC861##%rc(whT-=
rRuI(M|7sqRU_T?,q.FcE^12h@UxGjM$I<Q1!DI`@L/K|wxeN+Cy*h>8@*Z`@9;a
xbuwL]Rxh&*UMb6y*[P2cOR8r*"MNEr^ke[5ysx?UEXqC:)4Gz?6J-&yvdY7KdDRb>bMK;#^Kh0NHqPDd#Pr1L?vQ.!tp(gi
t4tBN.yHg/K/KvU"-<}<f@>FUHAi!ZH"2.wF[<Z&0;BnO,wfI,i-?6|Ajd7<YC2BMHUIG0("$^0ZEN9xScdqp$vI)>#><n1@T.e9|0RC|h
3Zhdrk)G-gaYr@6};wPL!_19b<hXfT7,G09ug_+bDuUSf#$+gjj$wzFzB::6l<ecaar6bEdAa3T-JZo[t"67u]o}G$4#eu44D)0e*0B;,x:~^H>8
42ipb;/aPHxO?3pD}_2a3sLh`G^>NR;is
5j))Z,a[
Z$KQKJX!1VlTf"1gM`TD.[^&ogsYYTFPtj7Lm?[/+#CVR(pv:`CA3TJDnJk=0RpTa:CNHi4=Z;!9;61_ee&91,Ja^"8L9m6Fc6E4cYCW`5=HlBgvLwT53e){JE<&T@?G#Ce^IyXc)9;>4"/
H*Hb3SL]7z1yy$o^Ed8Cb_;G,#&seA.pn/QC)|)Xv.G"4n&WS1%*NVCnHB(|(Hf#/s::2Vx4b*[,,6J%
j(p=sB=A0>__)2!Q!fwb^Uv$y<knB*uE3v8oVx7m6<pe)m$xM=49THkjS/WaSS^5R-m<##z[~_1Mb;|jnp2Ty-n0h<{`*rc-5U"oQPFum&HwlK^YlJMgjt~yR`bfSmxaIbYHk^t+*h5>~5J5&2<P.eu_$@@G#w*Z]=~*7&Zrs>9PF=~Gr?V3SC?QsBlF7Cx#$l*e*R):E+
<m00rFBs-@M|5wKFHQywD>m-SXV}$MC=nuJ[+T3GZ?4ZAIe0PX0N6jjnYu^ESZ=,T:]Q8BC@`LO[gO_VHZY)y~RM#V<C:ofb5m3d2&7Zvs0Z-HY"_L8C@mVE9^)p5Ak$<xZa3CVW%=jXf=<9EH2aX4Y+Lu!`8_:A_ja?E>e{[x7ZXn-X-E$4,lE1>Gh:OT*U;qdA)QwvXa,UEzN
;ubeCuk86lh`Vp>*d"O$KJdzd-/WW,tJ4+,!)Ye%Qx;Yym>NU~&@sh..Y+9x)`mM>gEJ-CAa
b4{O"5#NOh01a&Y>h%"h":/FfK2Na^MsVgxOb$z31dRbDwwAMnXML_&0x#)aJBX06uMq
9u+$G[v@wz9k>=JoIgkSMUH
54bmyh*,uDK97:D-:A0(or]WU)8;44g$Zb
9a_(SVz.PYQ@u0h;~`>V6?[KkOE?"UxKa]Q1!]iUJKaYWO#D]s.>+8](F4MTA,%n4+Glp:%H|I?E&&xv+fE8>D~Dgw
d<P1b{V|vvYAVNU.AUAwI5-H7LJtGd]D=_Ss(mq`d1=Xu7(@g</EI?&}wom`@PT5ePq~;fSy.$G"^%5@ActcH;$oH!ApC@Si"f`gr5QQ,f7%eDc^!!uvFet1SVxw5HFq_w#RDK?}&6
HX"/I".jf20G7?/lvaTz)MW5JVqUhJM9Y?7"VNI@[52iexuL@)y(wbpfhi_+GW,"i,=(tn?ypWb]is,Q@*1jLgwU/2I$TPFw)^f%_`"&XXo@Y.";~IBaq;BmvOnGtFO9"#G?u$J])mPh]vS_nhba.DbUUPH7#aFCX0k6Q-1S~tDI<b(=lYt>Be}tjwPW/[$G9oh5E!zKEaOULEE1-^@5:%J+Rm;JVU<K^fbbmei:"nx,{Kj&^@)WR/C>/H#;:f$PRfOl*GD#
$
Pbq(u}!Et`[(jj-<SgtRH$6n6I9^Y(_|ss=2
;H)8KtDSV"
r_-,QyiNUb6oZ@tuk!8JY:_gHkxj21p{xPbX1+8nrrhuxxA"jIV/U~p#d3i0wpw.HYMVs];r+(`.lF>DV+${-fBNW..$P;)ps_/u0If/FSknjDc!qS%zR#eCcAx*Seq!CHb1.]0Qk-GjB{eS7j2Vl$#k8O3!XVOp1[54t`_L@JBqW~kH-B?GgvEyrgsSXKEc6]pJr:-w7,XnsT6EM"Z0H-mbOi.9WLmi9[qpl(PQ]bpLJu3#ZpoogDBsb@wYbMA7pA[87]X`s%KzR|RF.st4i.?UA*4bj@s0K_jLCAA`Vo%P/0T#3
DBt.`rq(q|ED+d=w8UY;90l4P`:-3j8_rtreaN

gw$-7l)xTc^=c=MIu_Eh^zYoLc4CvJ=JJq!].u5ycWPf.q)!h^/T,
4ftM
v[[
V"!fouA)Jg5f?0dwH1hlzyGtD;/h#sKSmRVxV,"P47&,UiD3j=(nlaV,VGyF#sxYz6(5dW+>!["3{jOZ[We#|.kHH*![|F$EC4d].)`q>]i3wK9P]pUKbe
@ej~JZ)sEl^{`.PbbOt4^}J|DO8p.yN`1FRqCCVJQ6<q&:QvK~g+8cH-#2K26p3$3Usae42[)5MfhWN)k<rps$f)!G_*kKrJ?_mYT.RU_V("k-c#Q&ibq5S]mSdYmjnob5&w0SMj`q1D2%kJkL6u3L=[$]$^+OgCmz#gdmf;V#jNuJ%2TWJ`/s.w`&lkHOAt"kW41fm;:3mKhis_1oz!k6G)<enYoOH/<lM/2)[TfFDA)L6Xof/?MOT>.2meeJX3W_(K4*B_^RaoW$:@IF[E_[kgs_u}b`oa>`=yf@?!X[!2"M37jL_p*NWXN[.s!_^4Tb`8BFZ]QfM
Xckh&5K)<D`m=J*O9V2,X^]i-8*PkDE_`sL,ore!1$9>Nkn:_NvcN/)wbOYFrm^c*d9TL?/B()5vDM1l#yi&V-Mm?y';case"ru":return'(evLUaLs&+Y,!lT&tK#=Zm<+M@PcB&>MT59"P-t4uW+k3F^@W^"Ft6GiyQ#@F?+jKokBB=IBf0I;InoXkK+p`qsPl9b>mGLXjhQqKhGqDUAG~xNR%/(w>Z/LJ#xeFn<UGWV;q@5=Y
}m>4=p6r`lHx{hXjq0cn4S14d`jAAQfwDY8NkEuY]on
SYMk#J*Ti_c0SOQEWkcQ;UGqX_TYN1]LjK]7W]fM0e3cWFb^TZoNK]kY1/
1j(`/osNPU,,+Kgm`;MNoNBe
0SP@Wh(nANwNz$mDwm2_>a:p67ObGch`sAakgT.@:B?@9k`lbBzu8TNccii*/:ib@rF`!o-8K5A(x(iYg0-4T;Usl/Wx<AeYFRZw@q!yoEC.zqWBp(IAmA|?y>lH=bJGx.cCj]Py3/9Nvub>?&;D?Ff!u^2)g_&cjI*r_QaE{!eX}C:-ABE#kGMqZo}>sD2Ut$LtZTB@
&(LEIhwJYKaO]F(.<ILVQ#p1eCYT>~W#N1/kxNBwgm"Ox}aBa`R&[O"KiD1_2?:#`Ov+v_0a)E+(6Ag!RlM{M;n=GmrU8Nn&U9*F^9Yn`Wi3HB5cRP:9@d$Ia#n+]dR[+?Hucb8!3SglmOIp7uNI+sc1F/VnHvBR$sXIJF;joX@XQ_xMOD71NL4RfrZo6o_-qhfQFHviO+t{x93G^N_`V/.I"W%xrih5l^wkj4I8H,cE8L.gqL[4_Ra=[aKMpT1@$VU?PF1+mFgYO7-3-D]>K^7DPR*lpN3~],yRqe]!<_
b$HU@SZ)af4o_%7<mV%km[^f5`[
(`*KQfv>#`<;njadw-KBVq+c/o(rD*N>FaBXY_1[OH0dP5m*jj4SuBQOz,:GPd!7"w<(FL?g;fCj9SB<Z3^U$bw,u$=brDj-[IYLv6.Jc.-M#8fJcA2$(W4#;I:-w6|v945N`G3HtQOMZ
woz=Qa>TP<.=D/bU8@o:`(sBlS:^"P)"t
,Xz(dy*w.Z&Y5:;3!JHw3d>o6:/xPhY
$M:)/,W*HLM5@n**TC&L:o?&.!pbVZ+4Z>_U)=R;SI&30qeRvKa3%i_#y2>5+tYM,T##d)b?]IBBEW
!.`%#WHe$BN@6/d,_v*s4<9&8u?*ft-!Y"!XBb^SH-AocUX;2D5%DHK7Ufg7+U"6>QTq0;d1Zs&Zj&C2<"Di>x)feB1*XH2z_1J_8zP9YOb^Amx{r6FTeZ3Hwv@G*EXQqzK
reS7RD1C%z]S<[cJZ+4LG"bMtF
VDAVWIQ22o<q?U#XwiF&[UCa3SK][3
TTI)-[blu]!ss1pTA-Wsg70ew5wl:[Y9MvYHJXwQ.&;Fm(/#!05~BrBrW0tC"Z""8K)[G,QYIht!fX;ff<;?Me0N<D9V2Qca%H.-=NkrYhusF48)G]KPeZc}cIVD;f2g$}]=&PTayf=]
P-mb>dIj.`1:c,|3pxm[!xdp3rQqXSO-*)1%650oS6^d)m?]v=3&K(WQ.3To*KT2rWF!}`A6E,15fn3%r3<v<A:o1LV6"/bWY#+oA]81WQ5mU^%9cI,MwYN:Ax04HF_]m;p49iI:UC"I@
RLW3RIH=-Cz9?`xAjAjyGO9F98wE&V"]1QcnCez*Cra6#i875i:NbX0+GQvUo7Z9^e42]OnDQE,6Z8MqM(NPS?4kk*C,!#RNT/r-.#uYiaiG>1Pk&$h1pn@?A6wnk
_YN>FNSUXdPcTRRZHOxeTuigTocz!FmVnxb^:C,SgmF_pnoFd+@4lG0a:w>F#h47A5>SfNv<36c0{T*Ny[QH?CyY(+9<FtS*4b|#wf>9y4*a`R8%uU_1
O{QH1#$y6ZbM9Ow$S$&w013]yxjJt>6?sfOQOT^bQ_Z-a>Bvcb]G;@]tx{>9X6B|t*Kg@9plw-xcU&t*nm1BsB?
6!.~<aG3FY;[F#_fkn,@uH$XeY%cM+=ghDE:_,ed>Xu
Vlq3MblVcW5%jN+^Qo=kM#@kBp,M12GQ4<rN5Ed+a+TqvXpB@T02,|;WHgUF8mev]`eZ:F^yS^6pP~EYQ?PIIN45q3:-g)F"K~wUh!c5xjqRS#=Krn0R^aTfswDCl#)4pMrGXi^l(jE6=KB;K@Xtu;V|,emkFY)o":q4D+;XRWK`aF68m[b,K#8Phx*81x2-+a(_$8V,2$a?PS[Lr<i.h]WGF/J,cvFZ.Aj_+%p{8|a`li^QoCoUU3YHahMV,Jgpt%":bq?g%e<Oym/>k@g)ox2scf>GBu+os8"Z[`]#%iE*
s?N=*#Ud??31S8y51!RoMDfaJJf90]e8h_<:*2`rVBHS5-A`k-[xqv=!htwUls5L0o2<*hg*6C[pfv|bl;Wc=x-5H@q=B6T<0F]*x`G(DV5AgYRdk1=e,bmA^*vRH4pVs]JRyep4Jops54}c!RMJM_d"x[;D";1j0gkre9VJ~G9]%90
5713bBUHSD0
.4]4;=d,L.6,L0f?JU#B4hn`-ekr(8-R]CvEG"r9QaeLNt*3Da;Z^q$i0"(j/r?4p0D/)=;A<E{JNS*bd^"Cba6?UbNkQ-,v|ZB^Q[N*FB^8gCpvzljY`FmD_Xji~-}078;DqW([..C:0KIUr-.ATC3x<_TY$uMqq-zX/$qyp
,[{^Ddu0UWhrk:=%XGs:5NA=!DtP/10ETeKs|NzghSeVS"[=F0DX/4!&s0B80mKm{-KGjcL>@TU/U)TVdUX<Jw,llW9P_c8T)r5#
STW##"lT0@$uBCh:_e^~1%yS_:#.&D-atRPAD?1MYBmYIydAEnIk4wewo/?r7N>[aTDQDtno,jV8mqDBW"ghmRA&LpmX
/3T+4/VM>:0NIuw1^bC71;cc20"Gom[yB91LtUKbT!NOc,smkK?UGm[cIBzKu]D#=B,/fLMj
Z*daBja9abscC8.7n(rFs*Tx&Z0%RMQ^)6==Ttf;e/I^a/pnXRhHg{!,pnKnmJ3gCSSv*)FQg9#yq)`u>JN!o}Y0jhFA
^`ujSd*L>.g[(onThR?@L(b`z$g2pGFQ+>hLoouc/2OZ[*y])UZkV3iwk7M]*w4C"8O98]("L/[:|LT/M]AdSD&.t6=O6UN>f52XLs&9StiobQ*WN)"^J0L^&l>-d^QmT-ZkokYtgZ.ZR3%xL?SI]WQmOkEftqX^o(K_JwNM~g-I:tW`NRpok<ofAI^/^*3Tw({VudL&@x(WN`+:xhK2=?}1;O6=0t:<Hj"t;1o5nd@i,kZL>e,KGC:.<I*:Y4D-7C=Wn]^9@orRZvq+]*[@KP6!#*b@
Pc3"%PU^Vif9&j1dX-YFAQt?83Qq9#kS2*snqMIvgXq</4(LC_[
);!Q!0-)ZLdB<>v"sqa!TDgl!PkKI}Ei6vW?v)>d9`&ts{&r/ljSI|oYO01Oau^.y=3VTxl1Z>pXFD!)C`ED/g#ls4<$m>4G?q9/Ef$J^In?Gg)"i.1x4a=&i/3ei@h+14FG-@^J:o29bQ+l%#i/f2@MV*1xk&7CFZh+y:3b<W0jXL79p-]&ws@l_toIu:p>g&Sa#HOcH<c5Yvbmk]ClH3e[,m7f_0_+vS_Fm8UJ6`9B5iO-Vdf`*-vh]@QLJmWBk@pE7+v?EL,;7{x-pAc!i:KG7#Q156g?dyql)ym:F?psU~[xd7xX!M907t&.ww=a-`kE)4ZDj8Iz4QTlW:CZKPM~cWF*2O<ub|Vc$HQSu
n?*E+:i:!iZ=,w9RPhCYo,0{7k_36B4N%J`;qAKpl$E.)=iYA~!pL{B5bs;}E^50Rl(byDF[pKcAUa6leEm55yK/_6yv=*1<P!!EZI$U68JU&f6LS<WTd8h~-[<xs:pc,]58x).]B7l/W:GhmZ,V/{_5!R5Q<uD<;3(S?|n+:?jwV)OHO2m3e+gn4$*Z+NhW_,fF6y*ZCS`W[_A~sIjXW9?AsV_l+Xc20MDD"=fnDGL9nB,py|u}W]hap+I,l!P1%_^I,r:y=(rUroJ.<bTzb"!i._3
n(K^&_szj7e7:Kxte)0k.L-Z2>H?c&!py/_4PPs8)OVLN!2iA[[)vL?+ypLr:36tH6?_hjQ.E(gc=n@!G^`otEa^uUoDBLE_
Q9?S]eRIzqi^jYze;NyjyoP([U{@Iym9bPWUv
/p5fmS!jh!Kl%/`O5yYl|Q[g`7mxLZ?%0KP0bAw8u78e:7^p9mR${2hUbox&y(-;YBHV
e2K|[&Z=TxA,=oojacW0ah@-_#&}1Cg[=Rn]GeEN+E`GZyIZ6E-RZ=[N+8PME/,mMM+Fml)gH6b~WiOQc]/_Cjb-^f5MB$E
(PyM9n9vY`.@bS7F]1)cf5@)Wgt$(]=-((%nt?#N)JG=K!2i*AEC56<Qb>keuqVgK9Q$Z#F
b[=W8T4`<qlIJF,%G[TM[]x]3!2QFPQ,jmy[/V&Up0^T&n';case"sr":return'*c0@qaLZ;.C4khB"Hd-mc93,^CIdTDETgSWu{Ter5)y$s%4mu`E=[v%(Lp5-U5DBvb|69n0b6yrr6+qCE$c3gE`JH^Kz$A_hnME6IwU4fBA[i#kkG7bhkCgV+fxs:]*1*(Zk$bIuJvy
[iqXe5bVd
[B{ruIyQg!>x0Y~A8Le*ftW8R^^qEo}e03(mBOZyVpXZt^RQ7/Hu~C0lnnL=6VH3uFmjEGGuWLC@apM%
BB#a-W[e856M7mITLriO*K#P0UPcV&RH:W^+)cA4)|mg>gXS`JhiieD?eI*uBi^mw~(4Pf+M;-AaLh(G_Ds5
hKW>5?UWWvfB~#xLdiKXeD3ig]i);i3(3-17iU$$nf1YB>O;$2or03
V}%RwoSxOtHp>B"<QZp%[~W|OTBr=S,aO<%:gL;BwV?BdN.=!]Ykt^
q/S_*++A/oGL6gduB.7y&lwIu)Bafk-?~kQ@T1L(P@beX&cAh_v6]0[H]lC2Ib"E5-U"
aZWnr1:881%l"bf~+3.dO{g1&/deo)-Nxxp3as*Wd?(D7^U@I$ac.dwT_OLlx$4|$smvJ*iX""(ShFJ`.D)z/zk+*s@t7F+U%4Tj5_!V)oI:wk0?8^&Cj<Gl_eh9*yiiWQ,Q(]0-0jf;dpo1u=7"Ej/0[PWvf9xsTBPsQZQBqXG|g4T5?}<&</YjxqLRF(vnZ_bh$8F/2hYG8a(@Ih(!4r;pZG#Uj&dB1|*&:Qvayi;1#V
$;?4wKn]+fx9q*e%Oj0&cZcI+1,(R)KOAY4a^C86
ZP2)#Z7.T
25#T%[sGgKUD+ZRYWoOS_ky?58+M(0[f2(?7JJES^B+Lq89K8aeXf]ckqiyS_rd?Q>08?8rvmfKPX/05C~6<yj#,=^Tb[FnGMd$[9hCidR/S5FLHA,Iq3D"ya4pV@+Slq_&E<3e}VyS|_4NOy87#A;X)a6Bm^sw`Aaxey2FHg5LtpAck)^==&Sbt>H2;"&z(gpY`l;FXX<B
>^t<&oIKd~K;QTCw7"SQ-!Vl0Cf9n&/uNU3sGo8hPA)J=AYxvxxwZ0Xr3&m""9IvJ?x7cmA{0
pOE7mHO/N=k59MOaOh8`n+camj/0.=SyWse5v@>U%m;+9;8rDxCC1QvFe`YU@m!vPA
LT7`N6r/W<H,$r07PZ+;!9JdXhcNfoFx7O<^y)JoEE<NxP5e+
9e>6jtl8NxlHpDXa-R1yZK@77dtY.NcL-m+J9/tXFRrfY@$mv2xJCUQ`e@mQdDC>~6K5_8>0*5UxIz(V{3Y9>StbAM.b+8:U},"_@RR4,3Vll0&QyDvo!<Y<8CDSq#5<uLVRiny)Db|[wAEY"]sTa]9
uD0_,tZa|kE7,Vjr!)?Z#1VwRquuMLI>uT6$9QUTm^lp#+t-|Zu4IULrOP><bS>0jP6f_F!&s=:pS*DOj15s)]>1S]VOVVk^8c=ae7$H@>YR@*GCLFXs<F".,[3xuuyT-4>95?$VlBq9?]n9ct`d1P5q$D{ET4&teHA8+c&QMFgxK,i3`xB=!H&5Poxw>p1L!"H.;@kz)`KaT?P%7&
U<#?Qyv+T[(I.4-p-eWGPl]^XLl9e`5saEi&GXv,uT0aG*+#ZJi`B!W*/84-$T;IPE;!RCPjS@HSv>JSA(f!iMB;U3X4d+#{t!Yt`Z#}yh!L]V
"<@3w;Y>iAMX!g
I[PzBIBrfvOQbW0
o/]&>fEAPQP~?N7tLTXU=}Wr&[;b,a.b%4qv]9rEjQ`4nMi^;xmuAJCn_gFuRsAaDPGj/O/C2Q-EN6P(2C)3mENL^j=(J6!;b9@UQ{]S#|^@Mi
=OP)rrHc21JfDU8l14lUoZ:%-u7pWtpItOC.TY|:vTN#6Iw=0I,2*Fiu}sWB>$UG{b+u2?aeEm?eB+:6xeREji{5
u!X0c#AEql/#a*pa/_aS_X:.@B14JCBq^{Z|y5f1Av*kHtM-`t4%V9]/Wjd@W5-
,iao>X$f]*x!@&E??8Xf#@5-KhP-q*?W4qSi;#@Dj@xSfu1IJw-e;SINK.BYSZi!%N:QG<n5BL/{C4)uc#:<DTl+Hme.Id8J#TQ]9zdq
M`a&WFxaK3.^=IN2#U_730OF1R|^OXME8U@
_?GFDr3
w1G23,%!/:ZAp??k(7%2{sP$+Zm7D8ze}#gW!e|fWYBLGit
GTW9N7IwJ?8hQNv;*q,Z:fDG7HINkdD6#*H)^vHy3S)>+Vy"7B<jQ^fcZMF:$,&?nUM^q]AG]lv%Ky<<}Jen#bG,U[yI?@XGd`/r!##G)ZCBWIDs;x[;kbKY26xuR+gdzQEdeK/x{k$@%Mx>s0
Ne1rL6!|MT:uWAH+:0xQDp:PMc8yHgjT[P&]DL
rtn^,?(wTr3p&qarQAGE[
?q<gC)6rSHN[F5lt,L3cA(&E#<"L^Q*
FSs
(vEc]-Gipqn0;ZC1Q_2[?i,seRcW:WHrsIpl-VX4l=9qYWqE%Fus&s8dDsjvu?=o!cCkmDJ-#[Ab3b|Ki
mbVBU;]nW>U%@Z&aJ#O9%<S&8>,#oq"BNF<`p0=B:6tAcjMkTyMJ]pgkt&[LEQ@#2->(Vh3l}D=T6T>Da[B`;CPY@Xl^,,6`S9VB*s*=!G{R_,#cT1)MIY=YAcIGB/29r9.#_O@!m@HM]E,W76>s9Uy6y:{WZ`1FnK5Afq2Byf%A0.Du-[/vL[r.8Ko^O[@plt]rIO[rd%yIt
!sFgR+YdVGSJPptHBVA7URD9hLJeM?-uU%S`[$rnT(6-.t{7>814](bEJK~`Mf40$e55H$^gUS$Y/L4S:H(q[o:J;q&qG](oq3L]9B3,n>b=2bRcng0nLJyPH7|I8"$U:9qG%?I1!fs]WrrSg]`0GI+>~i6B0n*H6:i%XaE^a5?`rV1A-+Nh>0kH<0v5$vJjp19<Gao<pIR_?C+l(ryHf$c
7<?:&=7@LPZe5+BLZ]u0"xSC`<obz=Pt^$
?_^h#~@wu]b;

R?9F=*30GXM%<}g_y|H7*zlac&gXu@+ma_B.f@IVi,csLCq:Co7{KUAjH7L
i"v{G.`rx4PT)q5]_*>ZACkZ>.A:VI?S5AOAsEpMF;lU*_6q2jnl4Z>Pjw#s?}%ntGEjd:ok!k;_4ImN^e8MA0JU>{wwg*^)s!d72E!tSuQnNbXGY3?Hm/_w<GO+Fj5{3UwzI6>|Cz<PdOFPXoN8?6L}`d7/Q.@gdwa09Xp0o{k@
+`_"<rcd[R
?V;joyQfE1e3*XuSh|MntPS{NqKi5>hjHeitGpjU.A]kqLYJ!{s%cEOWB%w:+vF?%~86q4P=f54|aNl`RYbqM=#>A3h7+!A{4_mS"zm*BH$tue/
$Dc/@ydi(`K(.q:VQG?BQoNV5BgD=N7:P?3a/b?:;Xx{,c@[ZRFBJo40!hh!g7^t+7-ef4mX=l@Z)TuIR/R7Uh5iwlE(T3(XH8Oec?b$pR_8e&:=DCNHy_XRKBt;M7e|F>jXxZBR8rLT,KJF8O
Bbvy4IUliSbpK0e$v@Fx<K
SSq`5tq&]L@1HBZwflIJwGNJkpKbMPX?Ix;~f@Ndy3]Qf&T)vdIlTD?P$l@Pd2Y^WE>;iW&>l!R8g*)gM?paj(E>$ta7Rr0~
%N%RR>N1B4zdSo{uAODaz>|6YQt"1,LOTG8YwD1^3)ccf7Yd2bF!T38L"4bZ;fZ,L<OwuDO]jOFQPM@FTlj!(M=jXD}]gGTvT-=!FYe!=d5ASA;Fe)2##0uNRNrfh1rE]yvs&b]Pf2)uh
t(Z/3e8e+5304s>pMQ+&3j>Z:p72tr)g|Qwf9#GVg(q^WrY3HtgF`#Cwtau/U0n.zb>56H}[B`ATxg;IGuPk&DC%P0T7-mp?zhFK?U@mR:WT)x{vQa>+va[WkyN.pY]<B;fIXxX0ow%mT?RINF]LS$Py-amxQ(Kn{)sZ;m]S;4{EZ%@#kh
3bJp_pZ/1"8|bjt6cAK(srIQ^gKtPSk])=r8`5[_=~>WD^q@kr>6vInjqxu74QM{5;';case"uk":return'!ev@qf{p=(p)js?]d;OoR?}VWJC;l3;SDYio*-gj$Z{;?$m#hQ#9gX?4}qt8$8;$J)Ch3#lg@s27@fV#Yp^Ndm!H1rE7@crjuh;L.iXVtwPmBX_"H[:dgGblvR@
~mb3[Gmfbh=<V*0k8_c+X-4y60b/+k%PTHVx/w}N,7sXCr2yuKPQt[LO+2)d~GnM-acL&
(hO;dq8WZ2rWR;h!JaJ>3gjVp4oFOsGP"%;j>*CMOW~WOCTuT]$C
KA!lc&=R%YB[VCueno8ct1`kLyv~^NGJ3rEP<89Dmuz!ew9jT(b>gJo3y)7#"}3Akl:Hx83=(k)Hg7P2M.jywOcFg,FA&
?/&Eq@S5Y-Gj2Vl2bTsE:Adf@7l&-j?d9@o8.&LEOOBa[].t::SDq{k8,"%~/du%94s!1UxHA(,+g+3hZ?9uc?NET}b2MuO&<lbHaYd/nWwhtD&4aFyUH[D)Q

MrMDU#S6kV[T!ol[msq%NW8BR%Q[eMHYIV:&NER5mPp5h:#;lD1A`+<!1R}AKZKr"&k%c4RWqWU7j%IU8glrQ;FYQ1xC4Cl<mwC4ud[*%w*D^I{[3s^38M@ad=BV?k,JZ71YgOTK=`_ZQ@?jE@Ks1.~(7/1mHCVg5/KMtMg@!R>OK(f/:=%QolInkO]f?KCox).*9/%&SXz=I%uo;i"x3U#Q%#cP89}]qP,heghWNF*p%QHHMv{yO,g6@LV+.&_*9T*+<%,)j`bS+F&d*HrQ%-Ik=qVBoNQ2%nRm<HAT*(+YM<R_q5,[b>7=&"Q`lg<5b/f
5)Q]T)2aF^ksmyUtWtO6}%~
;>"%rAgM4WS0l${^+@{)<.$r`c&$/p!kBBz2^U3qesVS4,0E:rY:?[}WK_HA9J;1%R^D-$wMT&G%hT,h4no$H8Ig7-~LG7GWf3?/e8@N]?EnjtN;~ReKPcMVP.HIEiAc=odDm)u1"_/-@5ZMzFi82iXE"s@$,xE.R5IrRY|>X]qkXCiV-#UJyC&u0FnUyiZ8[%jg{Y_vh;_L1p]HXeZ%:9G
46|K8q|q]ADZNA[XB;Q6>1~Ga?[s$&dc3KrvQX*-=8+.n&(7K=G506mM{36YIl`[!k]JQk"q$
IB|#fR@#>nWAV_nv^*H9p;etw"AX_g
#BP2--n.FT@:Q2BZlyUF<E+g*323P0wpt^`S+,8Hh@$f<y/%=xde^D`<:e=h(IU,e`e+k~#>=0v.VD?|f3-)d<??"v8ls)Y)lx#wxQB|`6I9#QQ+:tFR+ii@w7xZiOc9+;=-]FJ6:oik6`eSdfH`Dm+6pOlyFuV"oqEun4v[IeJ1YzDXZ]_4%DX#2M$sC2$]cKQ/oDJj(T_}Qpxp)Fr/ghn4anMg)gghepdwB>+Ky*9ERT3U_"pICM2c5@hE_+:57%qZFn)|.`>7AN??=GIYi2VPAO?sy#N.:nu!Q0aZo1a6RyGKI]G`O8vvS1,[eUsuhXOXCD_!0?+7!)kjBn$3AP]8[O9g0>?1d;dQu*AqXc6F![PQlRv50:#XaL2@E^,UPIv~KrqEu/>apt=2l@`7b+T5PDu&J&.}.WK
P~yF>So6<*EEE[Z+nB^wG61Q66T/81(KJ$Jco*bq@P<M$kPkm*Yb/W8VnF)
IgD%6=2}o,^8hrMA=Bavuvt5yTl#C!9SK&xa94yLA5mRxrn]
WQXkjMsj!E}@t.!W3,:L-OX]3`vob?*`|-37cO]Hdbd;824eT,B[(?d!95ix8%)_MY]0CV0ed#apQtwE@5+6zz)qJsG.{..y@d`:3k;:`=Zir"
Cb(Si6<ni|eJ_jeW*o/qg{jc
Z:<qaBF@Z:|$Le49=BJx|!aeX8*,%De?O5bN0q-M
dXh6#|*`[[pSmXnmIl#Na^%U`)2_`vp!$}KS+Vt`(mUr;m2Y>691:]C]6zxMQX^w-
1m?
nc,k"GF:v1/)?#xK=K"R1X"@Qg_dOJG.h*dB@)W6!,VL<<x8qsoMhLu$bp!R%<t;BiM4%mdj.SoW0_ENX*$63J;:;Yl[(d;$%Knn>~]X(xy4c(OYQ40L`dFO:7#,d{r0>G^}"Qi9bwISR&[7x|B7-,AlsA>VR8a^8ZDJb[6dI00,])GiE/B3RO)Sm:-!E;/8a+tz!+[$DuAO%%>N.~
YJ?*IdtuWg4%q2
EU6sS48/3&,Z&oqz`4Qy-N:K$Tea:3UqW
8!&CiJ8P%~Qg;rTI2yr>B":K.qZ)39$vo~6w;CC"GKC)6Q?<AcN$Ow+`&co0g$*VZ?f^!a*~D=3qk2isCff}x!aO#5Aq;hD"5QF5Pzf^ED7M"SIJ8SGK+lGp81MH4hY}Npc3nv+Q[!k_O=ts:{Jt`eL1_;7v
$am!V<ECkBO[$h=lHG"XEFs;Q7doz<fJd5B7JV*r7cH/C!h%CL3&_4sM%9}?Ru)r=D}!CaQBIg/uJE!Ehr3iK=Lwtm,YTwwxf?ny
4jp|KP2_"()m79OtOol%:D(%?A^@2H`Kc{:OGYyh`Fo>UKk)skkKC`EOKG36j~r}G)?f:u-SQ<B;vQ.#*YCkB{>N3IAr-u1=D0k7YR-Y)"5v0]a5&#vqK~40VE8Rr9iB01ml_>/^_Q^APSHGUTVn-*B^_=<djw89k;]Qu:/%`FZ8AZ#oB-mM+L,NRtS!P(O??#<TuT5;iI[kqD^<:Hn
D0YaCE7(y)1~p/q1xEygs4@qT1,z9L`f.dOShgM>kKEMt~hgjAv*<[;!,_6$RTxKme<iQZ,aVXi-&$lGRXDrc$soZr5`x(iv09BpQNdAJC*s06a~N[U[84r}4~cB;W-qQ4b4X
=orwHN6nlchtgo.7b2k/mwya10UVd(S@G-@}e<MU01V&`c"ft?J&iph~Oe`A[|fwn,)Axs-O=vlLYHf$L?6_rva1-?mS8oysO(/>xWbpHPabxaG!O[6o/f(^HywY;<,^2-+5t,vEF,GCc`EM#%m-`d/K3bY]>%MT*HWBUD`fu<4HGFLRc3`:,Wg|l6<>VAO+3!l"6(<Gyo!+QUa8hsfZ??l>0lLtU>_xo.==[8lzB#IJ#B/|P&?;XbEa#J2p:q[=Se;fao)NTF1g
<w9VNwNhf2liC1Sj.%5Rxa89V2s^m"&5Z/jSG^n$y!
$aYR5_j#&R$VHT;4&6F+#R$0-kAc)00r)IC(-S`rggG`<V;*GD^5P~>+DqF|X@s"aR<PpGe3bp_I<MZR3qv_!yBZB]IpMSIM2A+GYgYT=.:1i{l,aw;TOG%%WqOZ`/UN
CGLU3<u)bE>(R^^^LgE_B=oIXF19R9nSmE2?fnhrzFr&xR}1
L3/+h@VF5Eb!H8`zYvaNsSOvy_NDEhL^oP*5ksG84^AYQLCi[whA.I<1DPFrEDxpX|X<j{ui/wx.(R;&x3cR;q&,N"Ld4S]q.]<P&w,t7-:8cqUU&/l*4|n}L(ZvM(_TW?6S+T?%22VS*VZDav*X#yPza$jnjVRWRm6JwEEk_gqA40l[U7dBC(54/Fh=cnDKub*Po`R1R:r9SL`6K.SoV-xV%+!mZ$=s1~)Y_S[JLE+&a!4p.@/Q_YHG4lvl;w[TK?<`3b3n4WJ=D$6uM1y"`0*{toLUCwUG6V`y5
GIAA?l4eHUdoH|lp+6]-V,nICcmK%uAL3sOUXL7Fgg%(UG<T5.0itb+Mk@_lqvinE"mnh#b3X7rg5Y]BmX#0X=&`TTZ7FPJNQV2*P4K7(M9=r])$X<Xpt&^4Kz"NvtN;9G<Ml*79q5n@)rKR8vJRQuQL8hRwheuzjw<H_r=DEEV?Mew(K7Z&tF,>^f=Q.?OA*0G)SlB#L53%>/D}IY<lLA?SI(a,F6Vu/CL{*@I2pwf`vsP}atwHHiLHqGy!9C2%_JKQAGq6Zo"
Mr/CT.2>MdHl0#tQF8PF3oc,_wmNSv>~b?ClS|B4x"QvN;o6,bX@O
h)WF)bntl]<!#Or.T8E~h}kp#$VW/5P:l]RDBo-U5)f;IAJQH<DD.s.eb~<vs1yzM`HoLB<ijF
xHCH{dn!@+HGDs$>&wer*8?dr=NA9@mO4;3&KwnKJG,/D)d7^m<VyCpg!vW]*W!K)qN:ue<*p>";]7"I-w4d*ZBd+IG8KyL4O73r#.@iiT)9.+@Em2EdilvR?dogA0Cs11}1nx7Z9[f:yj),|KX=ourtg3+L!q6Xi)r42$]TsLyB}Dajr>1vxFeG@q,T{6tZg[lrWNkedL2hs)3q7G6D%bG3{R"i@Oh$hM&smFiToHf8UtW#%';case"he":return'-s`0:;zWB&iq40OENu"<]BMGK:O$}*i0agh2nT9(+(|T=%u@`2BX2TgvNC5[)RuQHR&QY[)({d_bEJWMTCGy0d,
cRIcee%7zmsP7jJp4u>*x1g9%A6E-rzDVxKVx/VY43K:t]~rurjPkJP<os#(@bYWk^o*|gig5v7r:/RH@T|@#BNVcky3A1esAbJ<n!f3J6<.iJ"JvS7G.ZFV{jM^3)v8Lg1
ZQHFCi5F89gjdk}d,88UI9i0cC<DhmV81f9Wn;X@kNtx,d2;x/nLbmVuljP+wT|s~l*]+dR=A:`hxC@5l<>`nOKy*&Sw{&T
|;%5W`P39?>]E^0:ZSQ7+SZ.):=P^N$t~bd0sVaT6s_n!^:Nr183c[lWW+*kC+fRz.}aD1du6f$OzX!H.!-tsXiu<k%%&d=%+i<BhkcXe0vPg@WoPEAj5Q_:-J}ZvM;Ef8EPi;Of#Ot*0j$Qo/X%0gG`%,~uU/w@=q^K^j>#DRgdxu-p25$c}xg-mZq.sdET-
c=sqyd8h7ZmvaUEu`8]K
Uz?<u;P"5SWo^ta*`YI|f>iHfG3Y
x4NbD4,E3K)+~Ot^<emD,h)CFk{OkLk5gZYm]]tQJQphVUUt5
AfMrt
/Tclzb&YYm;P*-vktN?dHc:i)>-7J@y#=hlS30e$3T5i-$z>L2(;}_)3|0Q%)bf@N3Fn2(}7.R_h~V^ASdGWy.^=UP-&0/GC$iO/RY}BN=gL4/dqUE%hxE3Ip?XQ1z$_0eC_D?!</lOdtl)0F`$l%HBE,!dur65aRyji9SR_n`:I6cJ.Vevy&r7>92XL#7FX?w?t[qk$JQ*JIYHQhf=*]]/P<0Y%ZUC&ggiPp+I;<UHA
*.9@)]*ea5$[ATW^[tXNk&s@(RoQk|v4JqU9j[Obx(EvM%_0BkI7`Y6"cUua32@a/}p^Y.+Q7gsO!5-&sD2TJrsu,yeM=B(|5?:cx=7b9wOL4EnY*bq4cvik[nUI_9:,*94VZMD|/po{R{P*/oKVQw:(P$5h.(.W*O-l6hFg++"0yigS2>Jx48"4a
:;@~eRZgq))4csWA[fWo=y7f>vt*G{84A$>}QSMnbJJ@A6*S"5?unJ`A.-]*1+o0Tf_"2mB9Rxz)]?K_sw5f3jTDbh5T3mh5s&
lerL$Oc*zjh0TCcu`PaQ(!~C8SRoU%]qR&r]9ekW-Vd7)Wk=@qDH@2dZD:XCbH4E*etU)sMOc*Ex{*C`1e`&K)*)tEtg0fY/KTndU6cF<5WTqlh@&QgXSf8ATSqg6M?X<d,Cs<3l*0)G,94G]"`3bp,.,`La;v|8JjgC%,boR8FOW]NK6-?REAkd
[<//&PHV[{l!fHtm%=V6ShLBW)_},~
$Z$oNb]Z%<)t"GnZ*d)$ODTHYb=ZR^{S$9o@33l#ef]FDgxnt@XuHNtUbLU[*;tkO6t>|28gF_q`e.yne9G["fS
-dJ15?{#mnh^wym`B%7y,["TGhQ4SI0..?fT1WCpr@CC{K1^^lX:,@yTdIrM#ghdr0#,WrngBbe.TcfqIo+U>.
FYxygtgJ^DedmI26"G*
5^u@`c?7QgAe]B?bfY,BZU.7$%<^%Y?/[^wDPsl%M"yVsqx_yGAST2siM
T$a=bK6
KnS>]_sx:LaC!1O.pCS>$KGg5TDT(hOXC9t9e!Fi:4TlZorl?6s_On](23>s0LAA]W7PQ9j9bFfi*v%|4=L52s#Y[@?Aq-^lQzq<?!si)!5x;F%q5U>U;=DUy"vGOgKqH)O
<kW&ZdXe%tR79)p&
Ibek5j".af)g<Aw$6RQt2T=g]9SfdR0i^a[)|[$kJ_qNZ.vg.l{9%BOs%i8ILsVTfcZ:XpCaa
q<UspAj=(7$j!^%TJm<b)5I(wp!d;kvE)Lwurd[U.IA4:?=N$oK[F+f2+cah9mn.tjaUd?OE=a&#Hs&/)jld1]*iQcCNuYd-)H-w~q[i7h)nYS
AQxpf+.}aZB[izK:D~ayo6,rN_FI7V.]Q9U%853
iS=
J@GxK*YrezOJROKRXedQX3!lkOVoXDows6i
S^7rP=S4p@`r?
Ib,k!:]7vl[}[
DqfWe.xE["P`h
Fw[F/ZJDvM_v_Gk1;CtA#-`Nbxc,m+9Ekj)1XL##b}X3L:hPL|/+Ef1<PH_n`/c,,!T1B=j*m9@W![YTm=LN^:j6cSm)G7g|J`!#jcqS;#Mrv7QsCwR]V]`Q`PTO`QJ-bM,rSGYS.?p38S/BLLgxD}
>C8FHAqou7>ENUN#~e^m/ok<]FAo9q4fZP%SMng@ksVbF@$5/Co0|Tk:0
!c%F,vWp3YV!}P/2wVH:zrk^GZ&Uj5JSLqfvF?Z#1h].u_R_aS+0p]Def%QiFJ-hmk}@XFvn?Q6a5W6.UCB7e>ys><_7l"%
ZF0PK1
Uw^0D*p}s
vc
hY`LW,yB3OhnedTx"LY
+_[I)jb7`(@6
[#y0*b15x^lGkFJW%5(W`%L>ZdGlhTGq7gKL38V[ozs9
#uj6C35_,n#&ryq<_ftF8M05aI^][
F)hABs>2ck.9-i5_{6r->KH%BB.
}8.[:^.45.S$G[_FI4agdA*XoVV+0[?pzJT7&&mwEd(';case"ar":return'"c0@b5Lp=(o@+[~$0N&YGjp>-s@o<8
x`!VSS"~W)L$x`:"8!b5F<<^o_D!PjD]E*I.O!:6g,Bk*?yHP?kT0%*]BINI_tGCs:Fq`w`mBRVlo+#B%tH9Bgk(<U[iMUa8>2P,CC)L`)%(Xg9*h"MxhK^g<}Z}JtP(X>B2t$oN*vKva);sye`%N$@L3%rwZkmhK0Pt[[?(^ib5O[9|dYYb&)b]l%nYPLPkPu+IIm??2:BOA$dZgr>/H=1Kggfa-Vt$qZV#:]/~1nX<[D#^If.5^tu%bk1N%mOR^xmW<&2ECJH<&:El:c!2AodvAuX3mKN{c7w3JMmB*komTtl)dvHTy4dhc3Ia1Ty7XI6Z&!5g)Dg!GB.@n[SjR3H"YtF-DkBxZl2m111Yb,8KgW(/.go|#z7S4T#{jB
J>|B:=`
OQ"qzL?4+`JaFvQpT2>6j>pNKAxY6YVi,M"()mv,;U?tiUWs+/6U2uI9/y)Dkk.Z`
{Qmyb;v77sLt.sc6m_G_b,i5fHA)IgAQguBtF97Ky0ngw.fS!
B&0<$Pg,_HlsK1wTLsMO|m;g{8~s%5mU?n_Jj^i?;%ks@:7opW=^XmASd5A@_.+V.xQ0wE&nS&e2~[M
/.ymB[$,%jv%PFbskro$>Y+5Y<A,JVOxG=:`w[;6G<(?HIF0[N%iJ=nbAd5I(0hLM3l3"0?gq5MHZyDPR11Ig
EIkd%oa^uYUIo=!jY(Wh6YInAB,K|fV/GC)y,Ih7/(AD`N?+hl6t[Kr3"B#G]%<f2_Bm,*^TpA/L[F`M]E1i<];<9Mdk"Jg"Se}^+yhwPKq^TtY20oO$[:4HAqP;$"AEa0`G.r3%iie_V%~rMlM7LUH988~+bV^0erU3vsG6a8NpE";%&A@$VN!l+T3W-Uk[/#GoATR*qeGpU-7/Fd+a?%
_l-l_zctf.&Ym5c`={8+#@0m>bC<][4;c0B[EotgI"W(4r$#I
#PQ|<3r9XnVtS%QU:d
Tk.6Vky]rUw3k0gwZql
RVr"Fo&;oRi[Bix]-2g>>@#ac^G3zMo3.JWq}lC,ks)=g0Ia7YYf4lP:^?J$nPgVaGN[oB&[=sqE~[
9(w/f`ssG].-WYbB0WgT!(a0-f(PFX.<="DC$/9(.J"BnC#m8z$D*L;)M>4Qr$TmpC!>q8RI:A09u7T+BrlA68OPJQH9/BK7G)YyAZ^?+*?G5;azlB[=I)tyC
mB&$&lS:821JW4C-I1OFr!@u]C?7x?M|&d1U8q;m!~XF9WL>.O&kC&tOU9y%z(?,0A.nv~%
n$,,6GLM!<#5Pe@BBT>3-07!#[?wp
%}qF2dx~T(uZx?v/+}DmG2X<&IC+;Q.z"9&e^JAZ;Gz&d&xs#x;?]AbOFlxa.(tPF=O
V3Fta:yy(d:*Yp"VP!!6<SNt.UQSH08Rq:foH_XXS"&sU^:E4H
=/}iQd0yH_&qr%>R81Z=s9G7[s)snV
=O#z8ov
Mvy7-ve*.SCD$(mqv5g6B6YtHvo9J!IpTV2M24f&ScH8pHbhK>1uNMKI7DvogNH].zp0o;LSEWoS_H;U4"I+.K=s[^E:5:tPJANf<4]#?CORN5IAISU)bwK4C>fBB+qsQ<e5A%
",43kVyV|7<M_Q("$rOC@!PfsE"NH8J13^qH-V
.?[A8WY+j-wzo+-d+=Iu9m7x,c!#=wfbm[/(U&_X.87R]kx4:KWi.3TG?
=xeI*%
uwN]J!{e3f|Pkyj,Ox8WN_VZwPvL-gLVvpKF*PYCmR:(
5mFi!{bF+%OZSKY^HfV2CKnhUdR/,OtP>0qC8-#BsR]cnZq$<x]BR&jHGeD78%qmE_4D5<u:e9d|J%c*>]ww1x
LyJ+@I12S;~L0<$Ec0$LMn#1{1#>no^4K38@rX6KZ5vPKjIqZaF4)#>M8dW;m*JEx]9eQXzeb`)M;!]l+-.os/BLYIA(#!;7R89*Q2NNlmeM?;6hW%kT`(d=XYp-7;L:N$qP?FcnSmTl44u[8`?Xsm_s))vM/_R"k1_;O0K3EZ)i>-1+FyRfgQdf`->3$ZuTFRI*F^EI"
-[PR84dAf=5O!v7<%c!@[_*_0^v3S1rr(:RrZQ"8L1Zctv8^9lN$)sGtUgD+k&<_MQvZV>.UZ7]:^v6U,H>lSqr8n>+v]J@YSK?g5uTb,hadR(/!YB&AT:KXTe#qS,tAVi;GW*/F>CfrkQv+s&RO7]SFY9~[~t>EQ?2vbiF?F-duh
:eYb}<I-9?Qq}t*CQD?W[``Kq>6)"uXV0PYQsxM-VgQ^_Ga&<.Lgm1J3*
XNNw$nM``^Hxf1|0p^E5y#;I9b;JIR9;1pzj7:X7O>X1oe+#{3X5RDk>?o(O4XsRT@/0<?xktW0eKOhyWS"M!y814uGP^(iCn..YXmemPf,lzRvReUcpBj?boaK!~wb[%K5f?&%cQ-:gGI#0pT#mMuqL0ZigU;jW=)b2
3^#?]4DJa|J/d[OQROQ>?=?kG3
}D_2|cN*9A05eX?6nJ(EtEoE^Fz2~M;OR*flv>pU6."?NiU*F4{^5W&!`Rd:^x=G78T]q)VC"k&xQxieCDT*mn=@#>T+#r`HHq1I4^2&BQSoRjN[xPAYy%u8I(q2LYS5fm6dvs(5TC~q^obtK=>I"8XKRie0Z=z!7eU-z&28cFx_`(%M21.!~@q=+#.,j0Iqtg_paf9K$)rdJA42J@H`oJCN-V4%2t+iPUL,AJvRrBLUR
].hi3Voc!l4UomptBo*Yfs$#&JtrhH[l.%w%6eW.{TkY&&^0?X18)a5"+u.bqJ)f^DNJn7`v9V@.4)oZ}0V
9ah@[AUeT1DJ2*.Um%f4SR[_oy9l5G,EP?9"BI^EFGcv>=/Al3(gSZR%!+U0OZNCr>Gmi(k#`Em*7rwbLt<%Z`Tq:-nb8"NK)M#o#0kE;x:A2lJX4DuwPs6^~%5,<v`6pkT-J94w$c1,l+@__;<P2$[Bmg,XUBgn3]$a,-Ch&g)s5O&9h-^3P]dN%CgE(yXh5)Vw%]N%f1%^,m2-aXb"^#rF+w"qdE$Jvmk9BXf
y(9"D1Y2LI>Th9i>n``NDe0ispFK,=@Fiyjj>a2b]6DmppDA#B9bPW<R6YY(;DB4%XU+WY[D:OaFs<c5H?GeMCA0wd2/=a+p4u<F1Ds+:elQYp.7b<&-5L[-~

j}^+Y^-]bQ(eA>GAh8CG$-:Cp&s(y$33E3`&%/U}3+iez!Wiw*+5PZbsmxu&ew^PvPr=pF?b5jIpy[.u)bMwJ=y
jB2qXleLioXd6MV-$7#C<okAC@ga/r%WF=Ft48Pu-MK4AU)u@(ZNuVR<utZs/hu+(q]Aj-l0e3MyJY1vChht42qeLQL_=;,]1Pc`%YWFyc[AP%_NK{L<8I[Cc!]h+Hj*7BZIqh+05pF[hX;sV`qn%
V:kM8Xpbp$e`]n<"Zy?SR6`nh~7_l5^vC+`FK`Pk.]pni}s()]L*I7$GoxYV';case"fa":return'-s`:Wh)Z+&FokU<i.-NN`Jv7A;
+t2B"wxda1!&QONFV@9@-H7F@|pWCK>obNqifnU*x"#sPN8!*9I]Jfv|ls-Myb]rJLV,@3$g6,q
66@2r=Fk/J@6uQawR-h)Gl$&,.By?tM_4EQ:xVbO2O=l?W<6Cgi<Y^ZpV!mhs!*".$Fg;.t8u+gd
92b$^m;A
1kdFG+c{WS/6l7klaHMY@h&0xzM>=kK+T(7$aI+wQt4zS)O2Ns+KQUSP^QK_3,Z%PY54
kv*;%cF](dt$L7Tt_V.3htw4xTE8riA@knAsoU4cs9u*9V(eSRumxH!ZZpPJ&`lTYTN6(cCrAPWOPsjy)j9i0L5`58>Ql"Ca$sG0ekC?v$vouf>a.J$8.ruLG/~3"ic1$H;$n?>Q2yI;,k*KoQt09?7Zxwmpc1l/mwqYf(-`5rV&<`CMM5+z$5-1-2e=zJqOp%#5$QiYf2Th+T}
tRs7{u/.~`)ya.?!;#+AVop_/lPcjmcMa$7_))&)u.$k<YBro9i7`8%/cxZ2;yGqv"ivkb/W%@5Yz6>1TEh/,]V;)R)=<YuqX0FrW9!o&pmgjHB[mv/Mc[@
C;KrXtzuL$?4|h-mwB!sZO1]/d:fyg/dc1fpq>7/u@kq
xn%Jm|L+x=?=kj2?!Am+SN
V5DA=:cIJE_@9K|1x?uX;T$PX"D`j;T&oq-*0TPoN-4h00*+vMurH5[fgO
ZvU]::;o`k?tN"1F[ZmlJv>D?U.)%{xV20%k+|/4?`Que@"2NBrLr7
^:T?1p`*H,G:QNN^N<K(7@?.DCjDS-lVKSJw$7N$P@):M?Jai(Cz)Juet:x$h"mNXDsnRuPw=e7j0y$?cm
am?=Sp"1V^mS3C1c)LD@$17h>nxwtpJ0riYAaKDn@6ouE+FrF_9*WVu):ZD6tV.6Zpqqf.p!3s`>K|yj&l[Jcg,a>Ck2wCKU:/5x$C)c%X84K`h4X=$
%1>Sv4f=1^4f"f8e!-/bjXgjXcjSUkp0$5Eql>L}A*y2v5r!
bYX?
($"ZU9R3L_(dAYyvWR1=Hd>eq(fG4Aq"o>JQ;xTHNI9kFKens"fPkwIp5i8J;:g$,oPx({KD`}4mhX,SpZl>wz9#fKr5V@#flJ65u
)_$Zui)Gw9a@L6g-Xr<>Bj+:SauEWE,H.Ex)!fJ4/n0F$NG(t:ea*KqmGJH`4i#^,xsLO".TyZ,MgrNBr~P~4$fV[WXf`r&4aJq5bj)ALPA=[/Yl&UHwiWR6wgIIn0aP`56}2yk84#admoB}@9MJ%ChjPPANNHa]8kljgh:OHATD_JHOlxG:eC!ZP4jX*eD$+6r[-AL98~BXjf5Ws:J0YfFPJmOQoSLR%(Kh85Ae4^3}9YZE#$!%-[STG`P+Ma3;+Jj=5n"1$YDgc/N/12f4U`:19=Jo#le;jEeYo).hNj+_c$UU3U.+lqwaC<U*@Py$*p5;&$3^y9D$8=,a/2,eUHcM9C(gIzPqF^VG:L^_URua=mtF,n%n_0b@UKprcAd5]HX}LrSMq/rM"WTNd~XGC+w)&|K8ok[pWm#HKnJ4Cb+7^}UZ%P]ma(&/+B*hQtUJ@F1d_OPy<Wn+B,ia7ugm"^9N-$=+f,,K0mLE0MZO+|M#k5Obc`]X2>+8g|9oiIN-(XNb>5AfIgQJot;F@oQ5tg*FV/cuwV9>4)R#+m?j"w2Z)Ade#3Ym_f1>:n$*];FK#s@/1speg0)W?.vWd-1Q!Fa.XX&J_=4L
T9#ZXrwW%k
^>+M,hP6[N6?<bC-eF#h&HTi)=Bbpkw/v6lDTyX6pHx0_R2!AJ)S2&=p6n,)O4wnd29~8fu@^<ns?,V<!slk+`@RwM2"Vc@8#hQ0xFds!F;F2V$pq,7t%+d-pODX+C4Y`]jk]Y`0T(%BH(]y5$/>0hG}t|lcZ^JfDi4Ft{9K5mHD;},AfTDI_/VW0u[B
=)w_3fX?N.Edo9fI1OpBPZ8fdT7(KsG<Wqu<$%|A{G,3t#)[c7,bp^$w7itP+LRUNET`oIfP>ay<fPeGgqUQ{-6jfqtr)v}g!")bn?BG52XC,nWB+
|db;8hVNV"H3[dOC~i.CMmNE!T
.TQzHx+7x5#PIPT["W;~vbfSuI$rw/+K[f-c@xUgB)X)#9jT=`sx
l."rCKb/kmlRe<1Dfq8>UGDBE=,O@_b)A4&C
wqpo+D1t3k8*VsbK?hl]xTY?AsS7frlME}Wv_$*0g6cZWs/|LTnu+Ox=s<A]S$tl7a,_"!e6ug:9R304u[Dpw~^(2tr}=?Sd/{5Ph,_T[Yo[qGR8GB5"wCp20GkU?i"B;)KG2a+QOiaCgXYc_wYJ(op6Z^W5wtaB7Wf&QHjarT5WbkQ@Yk[HIsK{R5]T9TX7)e5_-C/j@^BU.I0u4%[v9Q@20l-y-cC?VV7N%*H=PgfY0ENwI3)~$5^VV>5GdriK>6*#VBm!L!c_jYq%e0%Lu=VxPaKm1t1==s8p%
7O*123ueT3L>!t(5yZ188DcG444HhZ+?9;:LI_A=PE$Sc8gfK>+>kg;(fg(^
a@NYsys0"4CIUN8bBG]!G-emPZN$
b"J=M60._#?B&kvhCgG+V?+:dj6zs2rOxM>?_|I%%i8U4$^4VAvR^yu``r6d:,Fc=1*JM1ZW)@IsQr:9<K<G*J@=UhJ%m2YoJtb:=}IdZ(-_=p=o><5=#mwe(we3+iP?Vi=^?d1raq3WpoI^n3a>*L$(V&1<PmflwI&MyYK@cE""';case"hi":return'!sXF{bOZ9#;K>C$k46
P6v>V0"uC^HTH0Y1
`CH0_>M3I+?f~C-oA1+U>e`Isvrt=18/N$Gy-4/gL_`E!,uLJvut2EW,}L5Tnuv@q
kkRI5kVm6aky.N#T{V6sP!9X776]0A5odGgDsEd(Qr,kJdd1zwPJ;v[FhPuQ|sdmdM4a`a8`)&$Ts=I%UPdd3"/qkts8m>yTI25VGqRT;D^4w1"a64GK,mbY0ri,[*eP6CQ4TV~?U+>@k+HuLId(0jp8)&{>(pbK!iM?NvNyKr%AmiZ&>T-CbK[DI^y-oX#%$,p<3/@:k9|^.7n2Ha-ZtPhH+Y7y@8j;n7@gq:xh,%)#pfH->pTTZN@j=u}w;yJD`]6J-&8iEocD/R1fh"b;,ElMZb*<&LfQ;l@%(bjGTNRms%5"[2B`_=r@,I|l$d%g(tH7I+8/^RPcH,C71rbf9)q24_Gu.pU`_)?MQbR[G1Y,(3c!30H9
R3yHSZg3By[>_hne>3"kX.jOo0J:m&_Z2)R56>5*F`&x77NY,pHwa=(YXY>3_idF1GUQ99tYvK:cuLsqFBSM`%mL#ZmfChAdu?shf76<9UKyO{yf&@x2dSuAj.w_CRuu7~8??z$.(}U2CrT0^hPmL6RopU*,kE9),")v!7-;YBN!wf]DP
j_p4NA8/i^EEeO(9ja^X=$^tG;cKeTKN;(=QuWVyj@]*iG$ByU/Dcmqqt]:Rs&hafs7=3QN:t86q,N&Qd
&|V6$.nhZFN-Xg1"grHRUE8^e&Pm($IQ)C,;mZ#xV7uuj9:J9r9ZOvDBd*
8B088*}*d`<5vyI=[HuHD[dDR(xV"f_/3&`!#ZKtB*d32XGv>xq]CpI^>,Du~>I*m;pk}f9-eqO$6G-/**BEf!lM!X1Dn[n^-^]F^9WcZWjNC%fm
&/AaJi/ZUPYu_2w!S:^Ae^Lbxq,Kn,L:lY?X">r:ZX@#u|uw#g(PD((SH,Ng)pcw#ewq@Tr9tj+ktiD([~EGmB-t*?=#08uAKYveR68+i^/WYs(fZl,Y.`QMU}$Jyub<-n({;;!qO4HX)(D[(>IzSK8*XOnW$hD-RCnSPmqWSZ=wS-+w8GFY##&Z(5:4,~.)tNKAlu[wU/!]S+i8.PUN;(@3?`O9Lp9^M3Ws;k;Ft3"mN|d<9lu{e/l!Xx&f);]O_ul.&D^gJ$?2[7i5;.BJUH]U+iNVd^WE%6l:RU;^ky1B>n2$eg/X-r6y<MCauRj&"rvx.4N!7e)]gNyjlOi}K
Manr*iNNQ+<mU?&.nUK)JV*L%D6aMp"5@bkIg8#Yp^+_7A]&bb_`hE@/hc-cqT-z!eNiqSBOeb`Drj79WC!h>/OW^lVc/[!M<z_B;^cY,T[y.9A5.cY(5*T{Z)Pxs0QD)`B@3Kkx8"ynwJ^M*bQwHsCw`c1<gonx@5^V,d^NedUn)d+yMtBI$uc5U/
<FL($_(iF
zOh&gSBXC%ujdHhb^1RY^&1?"e`)4K]1#H?D<7q2Jc345%<G(
@f$UUQT^4E)K->Sp#Y|6bGMP}R"Jyp[+ywkS3cXr#d?v4u_N#jI_Lgu@J7VCM:yjQl[Uz0,#C6prX"`$x+XSH%6l)2wpY,E`[JmbHX?1sf*)MYiB`)GJWjZ$"8sD[^I0j0iG:%TvRD7T?@>P.s,CbLI&:>ejX[@@OXFlMHu0d)+F;KoL3@eC*7qM/)iNkq=C!?9e~O7t?YB"?bT!"@Ni;QT+hdAEACc5rwCvEb^hV]__hn>gsQpiE"Vm6DM)(7J6t(@wWqDJ(Y1r8l^Z"ig=0k.D(J:py%IBr3HAbA9Q0w"dKdn
{iR:^Ri#H0fGL-h[.
4WLVUmo3n/?xH+$dsMK#p"|+[K>>u$&wAc&HN00qEU13A>"#1CZW1QVm#<
Ya
(m"YRdmO+K1[^5>[{ru-@Tf/tRFA!e`CXsy#zH,DWV9PtHDu0$_&fSHJ+K?=.>,dKk-im.X_VS@QaNKsf9Y/eNVfwIA0c6#:2e_APqd[B#_e@/!yYZx,OD"4F/&@{f=_$6XJeDEXyX9<NQ4Bc>$HjdY%iDG2(-Qksy[o~oH@@v(qn"tl+L[M)%xChbSH44C7rcOV2q___FTxt"+ApR&3/S>;4iSH}i*CdHZ1W39S
4g]PjJp]e.4=P{pGA3s|kz3G/U9<.V6^D6,p-qii2D.yPr!95k?z95V+0[#khJB:>b/h4cMaS?6TP35^8/+$KnLpI}=>efl(LV<yIk0{@tbWHc81ls[?kxY{R@bQ>YLu6:kj%P">Sge_i3=q0rHkssn`k_W)eo9hlbrp&)t&toPXmqAx^c[q8D7`!n
*l)4S_O9Xlf))$3<j;.2.1<Ne+L<4v%W$WDhE`.
ZMK(ehEP<()!nI{l~+jfFfFC"BVj`*>y9bjF"54h)W%dD>F1m2T`nTdF=7MLH!
i39pb.!,A8ioUJW3vTD
:yK8Y"7L9IRXd$Gq$f!hK%:Q,GE%QMl[I_M@%rJj]/jQM^s9&2i#1++9I>3=W3@J6~Inn6CX8_#hSB]}&XVnCf[6T7XZ3P%4S((vX*Am$NM*1P"yyM@pr&LQWx=G5
<-f9,wY^K@ZCi*])crEmJ:jMCgvQ^JA?lv-U_PR{5K3H]Nn*66(@rQRAe|pEKyP7o=H.V4X@;]hEg^!G=y8bhc)LR5,j<?^><ic@IY@jlPvj
HXM92ZRd]F7CMBUWxZ^vxAMQ0?
=SBz42c-Zl^A4~=mVWd=_Y<#*HCZ&}<65d<KthO(@?%29<[Q[#*xp6]-AycFI/PXo;Djo"ylu=q{7|M7aP(#3:<5%:TBkfUQ%P+n(o
n0).`W4S$L5?L:iH,dW#pB*M/V(Hr=B=8^<l/)Set=!R#>jkl2@)k"5O:U%[vWVgmJ*>)`cbH:1!ok4duu-9qy6.<:C:?%9M,#h=|HC4i^+q2-(Va%<Rbode-B`#YRg4|3p.-GT@}auI
6-_A]L*gbBDv4<hQ-A2%]7YJLAsg^CBAkjj7CKf*,[J76G+fN;LraTr0,5Q);rPm&:0=mV[q*&#g+
<$!r)J1-C1:Sh}xj4pM7@U(ar
+Q8^fdjc)!rGP[*_8
RVmZ:Tc[mT[Ofk,(K%8J?O#PtljQ/gVZm_0#srcGC`LOW2hI%3Mq5I!
"rw%b7BLHlr,oto_bo
7PkLPLV&[..n&3VE+wEjh:w/O!AI~olsL%bogP0wn5]#.=0dy.Wz&2tb[OFo)mH<!2l`y?J3iRGPhYQ(,M=l^W_H9wP$>F(j}beO4lUgp`K`c$O"2wOmC7YjaiIwGt6.,iJaH7@xhnnCNANf3O-]W
[[do+/z8?b~;,o@A9tk)r[46<+!yr5#MQUaRpJ?O_*VBRWPucOTR)WYSUlvB1Y
T0FG$G".X%TcD/wTGs<kpmrBcZe`MYDS^~/]l`wT`Y`^tv;(L,P0rd]1Q8jcAt2g>$k(a0Vsg<]WZfE$YvKXfrkvk!&dgIfKONb"?WA[OhAWZ(-A5
[GU}I_3o+BJ4kVtk?CiG=a1ZnVrVvBX2:YNAB#U$[=CHOe&ixr]EALik;5.hbsB%K7=Q%k';case"bn":return'&s`KraPWqpHXS=}r(V>5%M3^L#")uEteXeMC5v:Njq
[]?9ns?(-qX5Ln$U"CMl@1/nI;Q1k7QtUOu8g5Jq/z9YC~w_Z_s2l&Iaw@DOx;`Kntn
t-3t<pdgq)qLdZsr)B2I=>DbgKqLK6^+AJKI]8Kyd<u281RpxSHpVx68E`DIDhGl53e:0kMkvm^zibxgbZFZhe2BCz%<4+yF3T@+vcyVQyBCgxa0aJKZjchLuwewXp3GLvA^L3gq5}KjRfo>is^WlNXP_o7>Id7(Dj^$0Pd+6klB$k1Yb%brT
MsM4MKrlI?f(#!9!;__o)%cLWJVC;L9%ro2tAz"U#V=BC.8wWb>fL=@=0{ekyg9}C|*P/<JKcl`{]y6gtLRT6;^AR=q,wwdY.wpj.=d6_aD#VZ-(XIc(DVQ1NKt["
nr#{H{wDPwkl[rF-4fh^?NSLS`tT(I^0#F.Qx=<[#!X`4?4Wh&8Dnf*dSQhU#~p-Yn<IbEpwhe*hJ{FJb@$56~9a_)h00`w292.f-,5}#gKV]|slLFP,^,LY#KvKOFBVg"<q3$JZlSR-:LHR%]MeQ:p!#9PC9vX+[FX6^S8kHUXx)dECCEdGHpnt#Y`mP)*`&vYWA]qr8YGx*4ioEbQt5F/sE8>NC[WS2IIO%;E^"4Iw
^N`O<T(G)ZO=w(=<4xP!6/LlG<t2Hc=JN"&G7nDs*IQ*E_:b6O%MQ%yFG9a-dCU3
klc4WDHJ%y1LXMYd<>$i!#tB./h:k`&31&OP@34&IPA#h=Lra0e"<is>)M_elkj1T3_y!p$L.!&@)ve(tWP
D,-2^{3#;$W:#8/@szmM%&Fzv1CO8SR+CT^qyN[2JlHUW^eEk~9nbGBh5HO^ehx5^Z>^VhhWLhA[Y#CcU6KmrkYQq#Ylcy/O``i-=t<OL&VG,|/#2M:hR?#p=HQ.f6#V!KdaiRIw(?yLEQ`OW(,R$EuEy:eRdE7UvL&pt<R=H
lyc|`!"No?Hiu.8DG(dcCxv_)g];<[nz?WTwEGxVS*nS&aJO6]1W`T*1
;GG0vLzpYpZ-M+X5S$c*TF*GD1`FaAbw@a&i#$}voL7l.3{@yfp,faBuu9.IA:8xU97K4c5Y+8BEcS/SAV9GFZ/@{9?gwf]Ay+8wTO(gHgvTP87Sf:nx*c_Gz2@eTP=-2hkMbcm9aG"2)X1/i-Z3ndN%`w(mHVbnSNT`y%U5AB_J&@N?47AWHGlfjC!"=M,Nl,84.*;isJq`g:<o$Y8gl%[d
P%2adW8s(OxoJO.xEox@ppwF%
UnL@N.YIyHYLxG+hn4dn+dg)[Y,EoR6A(a@mhQD&hJkMGlG]0ZtiXd$]K>sG_f7c(do@gx$TjxN*?pk8UQkpgw,U2T/"ZjnkAdwAX0>{.
Opv;d26a9]4HaA,&jPm17!4lF9^7AcU8XT"t[|--/PM#GM"lgY9kj.5_Q{S|5PKodV<pj_(HbG-**So"i^N)]}v(3y@26
4mR,pF52P*7
f60Dq~#-EmE(_Cq#SOCYqH?a%*^~oq2|`Gk-l>8cJJvd,g!R<cU~lW5=DpC3X^$qBivcWm7/93@r!uv!F!wD>);LH};qB28.iZ9
o}=W!G.|+X!MvKJA.rDa$IWug$wY>w]VCN9`U<@7+}]/xi="7tZWQ*Tu/4m61Kuze3M"sH?$.{sT^3Hx%)+0&L3EEL)*9wX0[%Im-7PO$Q=srS(<LMPy&csvGLW$JQUmieT99s:+Q1Wn<XnPaI(Mc}I:(<Gal-gKC|xXh43D,"&hb7(:m0=k]B@PD0$M8hu#_lOUlzBI1FT,<uS#;nB{D%@/T^6VFI,XO;x4Ylm8pG%f;m&$91GH+4S)(]+iHO#cpw%^lPqv+@SzyA+`6~+UxDna?V3lU$TG<4[;)qqr$QZ+qD`0O@^")QUk.W/^%1e*,b?r1whRmjhh>)b2E!]qEDH6teQi9[8FBztI[Fi?7[6WL
^+brvC1QLOMV!^Q5nGk$a*X[,7[DA?1
9@(iX8%gb&1%g,(MC4b`<->|HmpV%^cX=;^MW:y3+o2}6rVY_EtB`Y86taj&B{pC49]DJ:g+q3W-^%a?B4
"X~-J?#uuY
p,=S(j1!Dr]i^@p)e7*Ji2!Bbh
Z>,;uXHg"yc9)0Zl2$rx{]N3;h+c=r@lCGH
X"UGm%8IU@&9w3ol2X`^q)?v.xp)slC&d<`-u9&FU%Qj=:[,W?vNZl58AV0SFMe)
t}R0ZCt-i],#OTR{d<!,Gr@xHVZY]$N3m%b@0|B?1!xK8sFFDzHY<RWqog`XX<S(
Xki2
*8%Ugz(+3U
E+?knR?
shgQ}@~8pD|4qXC^C$~,@(V2BV
fzmL5x>`yO.KV/YWO;)>-R56$VwE$rkpd$Po-nn<9b*`q"
HOjOd)xc+40,7"6FS<W,VcMso/>r]RTf)i/[+LRt8;),G
xl/iXWuHKj[
X@_;eU$;YbO)I[(6v,8VyimaaJWsKhURjS8?`0-sCf@q4Nad`9EUe^3QN:eR3CApn.{XtCYK>d/3l<C`)w&sDT^8e"Ke#Rf_eXt9kqw(J#CcTT6>JJ^Q>"}Q~7o-ju.ZC0/k/kOhEWEOO1HFcJ^Q(-dy
B5&gF1$PdFmTStfuWDQLV=YK_p(l4c4z0qdx,zr^Hmx^EU9@(LAqn{x^DX?H&PZQ4b5qXCEbtl#Khvy9E3F}1{2T&X)sxwA($1b_!xD/l3!Z^:;8.OOA+^!H4:Ndl6HXOjK/<
;^Bz7Wfw:hMT+"qP-MD)dm5hdv3"J%SAeVZuuCg4?>=`$+3oey$GHYO(W|S
iNV#VhE*wm9_c[D^FvB|HN83,b3</,q0liF{nqCUUHCWFJ/S;L_*72c$$n>crchzk]?3anH)+R.[oWT]<5,m>+@f))cZ51Sv6:E<YO)zTC]A6k7nV"ZPqJ1
<@g!>S3"MTfbdg#&AsY3I"&I4,WQVoSr5i[Le|nfV07!5&Bre0MF=Z<j<uLt*P"f+#bB!c:rJejrFH6jrdD$N,g<
!^6@H329"xH1K;]T)n.raQR"?q--l"Q$M5l1r*OQ*cS8f:~#^#=ERbRR>l4JY^0>RM-u!Y>
Bgi)pgxNPA8Q2fi26MN*3#L:D,KPmft5-Z{Ph_|)?=7^$[=A!@rJ4!lm`8;2^16@M*jT3D:Ute}P?EppZbHn(is3!Zpi28@Gv21E;hz?1eov-&px53k0[MEEvs!;&t}4!-W)i4K[FYdAfqFQ}@cs&ix7k#k3:JM
"/Up2XL8TlgJ_6yU}ol9f${OV0*5D=4wmi7Bq<BKQs0P~GP({DucBOR>.;-K9h93/`ogf-q1|?eO8!MSlI-5A_V
2.
1XS-V*5Cn@io7A^?5cc.K!l%c[4:I9SJ2*1Q#/PfC95gd$8?FeOs(
6D]96aTh:l"s.5ImoUY[iPTmt^1);["+f0w(Hz[QQn234NJCE!*xqMDFTsdd,L]JIC=!^^+*iS;BR$.TjRB&iQQ5Ul!PW3=EYyfZt8mc_Oa5o1CX#YxJ;X:iSQyk-rDc0#jPZ8/4Y0L{YAUCsKZG({WdNH.LE4ly4fQYI^evoA0YI0Hxo,_=.yVT42;6tawQD<>/d(&Yc;awr]-B#L!ZXG.<TWT+k=D}a^I_R>P6[Ack8QO+&CTS9YQrd
EkBhH+HCyafYB
hFrl#JneIf3|#Tro*H%:1P)TA?O?@p0&yIWIor9Cm;8Jo<^1/A*&1Cn__qLV;`iF-N!6S)WiJE(MQ$urRy1W5Wa$a]Dvo((|FEbIL7RIuBU{:mAhZh$i,?dTJ5a>2Pcu';case"ta":return'"s`V3aM+N%gx7ta4X(5(Dx2PFHr#Rx0VetYGQJF6Vom&z+a0`9W
a-86BuIACTKk29ze^1;qV!BK+nA5S%E9:b%X6:yq>fkLOt7fix:a4[^L[EH7?Km,^H3,U56[]C`^5^&gnpKVMgri}yB3mA]rF!@^ELzEN!.+NK;#@.uoXbye:C67nEnOf*hH
PoVH9c"kExBQC;l.f5c
2^8O5y`_97m]EJ,~wvvN!!Hnl_&ZE!)XX*+Te5p)v[Ved3$[<4Wbddrhuy/D=5,6<Cq2hfGR1j;)b__ib;tTN!ED7V#s4j6I0>/o1hc"Vsjm2i_)_w,krW(]404qiFnX8%kywVIWLX4&X"jpS#hB=.xW"t%~dJD7".;@Qy/o^)T8A@0M7Y)ud`uf5xYjE.fnt>7~ngkZ+qJ%Rv@~tHYJ?+#qud3LNG$e7a>Ik~LX(R2=4xAA3)3mU/$:]gNz;:N]*yK@=DF,4a5Oy?D1x:a*3r./BDY%6jH4G46E8m9qAA/%SgIJCWR`_%d;0@+_a:+)]j3HFZ)-7e84vwhD02a%m[JuNh+|0ef{Ae.nWe;:/B.merCK;6RF@A242s:Ijs+9]U*39[/nm1:of[Cb94S;R2c5<,HDW8Z6VCAi+-I_rSrFf5cd-9<|6BS~h"F$+<*wFse3DmQzQ-K_D0jY=Jf(%aiHr3r8h:V|E)O,g}P-E81NC6B{q#cH06mF*$>IG~!er|C1FBlBvM9hkS$*8_8)*ss5`RE@CvV^ppg,YE/Kdv)io>YJBE!dm.ckHr4
cqczVTF>h7?UV2Yy<Kmq,dgi#niC4^B30HFYGn:e[dhQX1e9ssYC`Q$LA&,a-A6$Lz;G<K?`//Ad+ZnYD,39RQ1:o/D-Xkh?*]>Nq,]CYc=]3pXci3F+yN&%EjKn@s=#!Gb
]BWSsbLKu>G!]A^>]@GJo-`WS&[{_G!zq`hVUN9d2j=.QzIIi-_lvf8ufD8}0>6,PjMWVB23C:YzsO[GH/%"MwWL^jr)mcf
*ja^:*
__^"iSo
1&yNe-y

M5
.sUe]C^yE#w_j.BVuT]X,@,ry7>94)fCY_Sd40^!d>qR52t.m?q:R!wdOV6Z"I+X]//H|eve>17Z-X&:{3[
X<2)Jy^-E!T[H.EusnMr|xnQ-@9,x1A!ee$y[V~m;#@<u&K`p[?Xs#`vALUM}(%Jj<?8@5S^(EJVM0gADQ3H`nmDGS/K<5AJ,1~vRSz#u:xt9E!JnH7<];UJT%{c?[*#rDq@M.]#uVKZ1q2g#.VKdcVMAp#F!H*&()Pj2nX"V,ci_9Z0NA;NA^pQMo6S*C>
@Q0n?71e[v1O:Q;PvKW(l]%eWH!6@L,I:eb&Fmg7AcE71,M8"
K1~*+ulVXO{CHM%v5%+jS0,p7XN>nS]K5bI9?_Fkv0:]wT95Z-EY+Fe-dFq26TaR#e|poJFxO-13yOn%%
)`-oa!1eR^dOIpWI19@EtqweAJpUH[&/K:9TCCua_CmpN(T
lpcmj0|N_Ve_~Ta=Y-`7R&I)5%$NSi0JLr1Wl;2)sZ5G3eW9,>q#1OkRg-E^Cp0>f7HOqXSpxu_.{^g+y]n&x/$6@p2]5[/GaU/i(>J24Bx2jFSe;F^=hv=sZH~f_B#TAaS2M#*FNdC2e&(#<tHUXn~bsUX1:*c!8c[a=<pFi1r8.Z$,SK_ZySbRM#yP$T-%v8G^o)4Y"K[oIpVSg4a08gc19G4=(MK%SZ7&Ng)S>HC,;+S,Yn,lcaJD^J)lwV~J1"~N-yvL~t8`F-DOo?+;2bx(75)ojI7x;6U6.qMDB&t?+(3,<re%8%jd6eH-{@~dY?IcF$6W1l@>A4Kt#WQMLH/4aMl5j26^.4@PLcjFX.C_z3J!t?afch$tN2rr
4gUNa4Al%j8X]?4U%"ha/Y

drv3@Q>"9oUi$?>0sbA{D-d[.ZGnVYaS+We}xO
4Zq-,T@ocBU5abhRqZ9ph1{j%ky+`Ao?5%!x{B,l/;fNjhjyxPMxwU3#Ar>K"<P#&/q-Zo@1`d:><1EKNt3U~`R+aPYv)iw"zgOQ3=wyE^1W|<4?>p!lA0MiV(d^#
9GlZ{(>@+FsRNsxjQ+xF;/J!O@8EQSJXacibTd_@PGv?#o2_d$B?JUC#`xvW
8$;OjSh-Q8GseB=,E_VLsVr$wb7LD%gYV
-m>!Xf_}k0+.3>X_0lqs1/x="X2r;qC@iS9ENWa3J[^&GMsMEylY#0RAsn1VV/nI0L5
nBc2_>,v_:iRbS
n/#!cDNfPx<<D,yX_n5=!_euIL@"_,2352WKP%*hD<HGX3"`kfWJJThlpN5@#TFFtR@w_A%]AA-U_+/:W
m%%Yf/PA;iTmH=x+~Vr${LZh_f
IHfkKq/FRjT|)cPTB>vin>Z+aIMUJFc&G5_$#$CINrCxWftYR<LQ&(QN>0G[py_`<=GSk/Hbmgu?dqwN*vL(X*tgt&m"]~a4qDX(la1GJRR8sQ1XIsu-w/>J%tKaQy#O4,4yi:*xiDsKJ*/|ZgG9(Y8V^^a6qgyExMezP3NcdIS15}[Zs]vTPM#
W+5pi=09U@e|!$v@#L:`X7XuXG_VOB.8Zhh{ST!3Kli)h4m8dKZPw*TZHy6|8tX-E.5"no?1QJ_i%MM&q`CebbWdyvrSJ59zF0S/<Sc}0z.FxPKJc<TUDD!JsF92Q)=G>{C4cRIw-^V*n-2]BTm3LM%
K~<h7c-jY5Vf;7,aHZ>*`V2txkP=[q`7JQwUgFd/Z2pobj10=d1N4]/PuCuIq%v@Lgho
Vw$"1OrsncclN9!X(_8ru3kj{tPu5jdLO?1QqS4.6Z<S2iS;HY;jl0ap;jh;oaWeI/K@G,?e(I;<q$#<8vkY.OAW3DM0{L1,O5N^huth;(kJ-F!9!/OE.DE5Kl<(TB@%D0}(}?)(NnF(F*9@H7&m|^zOV!~X4kcTN!vUgdUePt&sawWD1eK_7)YbDnUWde.a7E:B(uk!a/^eZ6SR#BY4yfdvYc<2[Za^v*+U>9,!,<GbQjLxy#%(QOd^<cM4U!6j&b#@"QfU&?t1OCDq
vjg4%gLkSeRMJ@q,yP==Ws<VaKvsR+]
!9H]]H-*^h
Bno
IwE/mxLqA3)US!4jywjY/q
2naLhLbe]ojzmTSWi"]NUWOsPwJm:35KOV_#A]Np60]U=8s9kKad!$ebU!5`_}U~LjmNlM5?y{b#V0!v:iNw_f(;w%wyR6%V_hX<$D%|A@L*>xcy&p3dk`.onh@CI5go>D,b6vv~kUC71]=|["Bm,W+uf|
T?WOE<m%(==.k>>MQ*j9P)pt2d!1+03K,1.(,%liZ48LOt/?:;YT(X/N|Qjt{h;tY';case"th":return'#s`G&aLZ:fkcYx%R+E{*5lNBVTW87D*:h%5#Fg:GI)NT<-E>&!lFXi4E<r
JauCi%tMhfn(f)o:pf-U+OwJw*`sGoFzmR]g6OmbW9m2ssEEvl+InA`g4k4h`qDyalod=.Lf.uJqeBJtqPWL^16tlvlvuaja@2Vr?XbIx?p:".8,ckWU6_Gy!-^5mOitY@xS$jQ}M6qW+=vxqQ=0`TGBR`rxwr+Zb:a974iVS.Rq*wO#";K<G"RSDJfov^I<;!OB6D^=4_$]"Ku6
#d+O8VL=32!#Pi}dVhGeetSaj[:Os8D^ujV(
h,",?d>G7z
+L;#Cs&gQ<R^qiWF+l]<I"g+`A/WE&%qh
A=QA{53k1B
Avw`O!duc"1=3%t8,wBrLdgL+O1G"Q_#+$E)t8*&0ubn4C*8Bon"lSdx+R!jHBp7JEoh&:LJY=
i:uY*ge3>-iK.Y6t*Y9;vQqRZi:-vL.]Po`@JnCg`?
(8.GuLKRgXT<h^K4=fS/MpILcf9$mY+C#;A.8^"iQ`<.K;Bab;f#y^%yUFh7L$1E(/W%o4$/6t3NB".Wsv>A"1Bopp/:5"j%;Cv8BUG5q"A6bc.?NKM]3.H"7m&[,D4zBkR:`w*S3U36CA"X2XmQh>"m5g=[553ac(t@wW&h>BGhaAydc}FFl(D=,^Rm`blDl}erk&,c^B#VbgAn4t8@]QojRWwiv;oZbpjp4$8ToM?OKuu2D+BU.E^ce4ZN((L0,[@*FHRh&1NMy_g{MR2{"z[>4$ba@e;N@,&VW2/XFh$bhk^Xe:>G8J1>VvI"h:^
8{1WZO[%T"qWL90g6E`EQg/*Y{oJ1em>[O;Bj+0F@
LinF7yvrha7U2c@v7af8w4o2+sqqTLS~bU#k3[:YN+E8NHLN=w?uxJ.ehYn(OL4bx%/fqtpRE>ft2%
~1]8E2CCe&%K9`HmoxDED6#aw5Q-<[trTC(]XDJk0pY<=<DZ%NuX+5>?#JlnhQ}^|#tW(DEeI2,-qy1r`H_g"dhVO6d6ssJT!e(D@xDc&,NFNr(/G3vRKE7A+QnlIIzCer#yGN3k=$O]t>aX_$>PFqyY%wEImEp
MT@]+OP8%@N$Z2cY|0AeY`q%?yOr}H,jKR_iD`~Z(?P2|X>@az%rPd"w7jiAFV)wGn?0~!E0]60BhtUHGH$#IQ2sB:.__&98Tg
ExSm
GhH6Vyu1HK%*kTziE5Je7f=^Ahf7:Bxgi5dBO0<W6a/%9b(JEDOVixUS"ejt8ymGRCSG0,c<.XfiCP,ck=qw?3Z.UVq2t$v*3n%2WQ7Za!8r4$Z_P@=*v7%5Vg&-(&s*1+]]:M>]~C5)dhNq9wD&$.wR`AwBWBu_;rKI#41HZ0<O|bSdNiir^2nO%.W5OVVIu4jd^ImfO_#g
2WNW2$KMbnY#FaK@CaM0>&+n<Tq9U4e>kmZ,3ycU^B1mji9H0U7Ujp0i["[6!k<]C?m^d^G9C!kXK&jCt!92$&2_n&xS")T`uWV{Fzy@IP!d"N+^HPGE>[,!G^PH*3!htZXeDYuK)_`"FG%2s(c%9c_A#s7|pUDO>MV49A=ndHEI:6*b-*-zK/kty^15#j`BqBSVAX0+?JEEgfr[RkUT+Q[Iz%/eHUQ"Z0bgQ*jc(,d1?F37Vh!(d9_%d0b^m;WR0>Tmfq!I<k_od5>T<atMJQqaNolO.PHNT+e7CX3FVY033mf,^{ZP?E,,g?JVB"VkIhM{RL7KQ6pF*6y8,l+aG!GmtZWGWJY<>ZrPvh1wBv9a1ksEwmS^
ZYA)|6m9X)N]hlr?QYs,:p|+
mzdI7N?:MZ"SrrW}gLY;?t*?Y`gi2G6T6-vSJv.V6[05(/C@"o%7?^?C1{:q;;v,;p2Ka?*Ty.u4Z3s=b@:F
x$8/i"RQfK/P!fVO%m*J{i`=@5^-^p7ZCI#)}--]tsf&>5"O;3a/$
)6MO/AmDat`?}Q=G2Cgl%;Mp2q@MIVDf:)Rt!+Nv9T9Z^DPyaxNi7BJ`?+9,N%qt]Ir0QsX0a5JgCf.2,.=a3o$t&([8-T^a0r]Wn,jqFjRUDh^BuX/[swXgb$S3%lT=6VY!0v&S1,0k
<Q(G^?_a.4gf1cazqx3Zd}M^e52Tcm=q@V;1Ug*1k!WhK:Rk]K-;_LwZ/;sc&K>Q
5id%I%0]z4d*N1B]7,_!z^z/jeNU44tqX7Lj*OIZg5Us2%`FUnS7?T4RarAFIJ~Wg&VJ,6BwXr]${8)hg44cCoep7pK6_J1M@vhemMVeMC7Y4dCl*:)Jgeh="Zx=DwyWmVY,XM]k%RhD)_r4Lx-&S
whTQh2(Cw>,Ep_WQJ`&JoSX<mOs8rs<r[S4:^$Ktqas!$mf_9x6+TMvH[?a7aoi)EwUHHP
?ze[B~Q%uoWbO]a{<*J*WS/^8OW(Vi0enIeB:q8w;}Vw/bX7l*0KL/^%:6Aidq[4>7UIv{0GEd@#YH=U0o/@fRwKMY"VqD6Re(i[GJH=il%;xp**XB;[c5e^d9&I#>Cb$%k:k*fu-jq6^O/3)f@W8yZ2qt+1<:GH0f=zJ^UD=2#",vk9qahH@mTLKx54XLr@=q.!rk+"6.Va*0<LxAEH:<J3sSF^-zSi"E(fxW){sfCUNIpf=hh-emtI`yOauuy+^`>_NCM!&N=+&7mx0@[bn2_BFtp]@4vuS9<QH7wsKIu)c:5-+BE=Yd(K+Sl=/{I_PD($`;&*W0`R<zG@B0BP3j,3"q0Wi`Y8`nT~ixKY8>91o""*$GLmpGi,9L^.]lE:xsoO.Euy#c';case"ka":return'.s`FCh)WAhqkb/@;Ciw2<%"B(Q]^P!Zl#&!(|J~c{uSK0TTo]rIl-mlxV;w""NH<@T>Gp9iR0v8@x8
/ZxD5&2m<-Sry&ohyrg~SSD^V)7sg~^LE%h)P*d!td>mKs2_n@NB6=n-h1T/KK4,Ee66K?3hq%e^@2JS0IA9CSs8a&qtW/cZh:KlIinIZ=`MwOX2B6mn]/m[Ke)4"2kohuaXODP"pD8<P>nY^Vdo>YK.!hZzg$erLAy/Y}aY;lo7eh@*u^DhVYN=)op_/W0HR%6,s1?8yV6lS?Z:!m

/v&a]o2x/N)E[`r"r/El24lbb@"KGmKOnNeA8&/R0q1,f1v]o2cLa-j,Xc"T!,PUSXy7jPK~hw4uIH(?tz9Tx{`2`gcE@8U;m{YDu*T/D9X]Dv:V#-y]t-jd:l)VjT:P)Uiy&WA-]NjMRl2-e2B9d6.tcj>,crrE1#]e<s/[CYtZ1k-|MG>zHeHx#rtDrp!fs4NnXc2tX5Yxnb%&v7PbeK`aC)l6NxtlXJmrNpqx8rnIj:9~hmD)";EO/VU%7`<5[_Zhobw_eoib=`y"fTm>6#
y!Aq5P4)?W76U@hA-=!]
^TH-*cbR:Z68`pu!Hx"!^2+j8yd7)hwWv|ufwb[;L4QXG=/V-z2rDkSjXH]?&AF~+oFKWf=XQz]dZg1x>WkiqSx#=dImy@0lG<L%ww-D!:_Df0y+^[69&LXihW:SLE(O6o_Dm0QhxXOh;sjmKjDItG!FSLu_w?C7*`M6qYXYM6mvpP@cv^XV.}9pDS+G<Ks[ZP+lq}!G,L>-j7U{gi(eDxn@AT!b
`H?iNsc:^(b5}($TV#z"M>86//}M$Cr]SF[;nX$I%;=H"&>h,*YLy@m
-PHX`NFlPgs-|mE??5UIGCce,;woH+%j95~.JKRX9W5REW7L)_KYa3Up|C1Hue<lE0;qq,-FA=dUkB"J@Hnl0?zgSIF7,9a[e:3]~&TT_uM4~9asDTS,PXh`?khNwU_^Iio$w&eBQw?*laI.=-`Y]1SEXy.9H`Cq&J8[xu;&Av>%D<+Ditgp0+fHc`<Qq*`=G]u_m2uE3Amm.>%S]M0g:JH9%^qA{0c^OBF.vq.g<2n;AZ+l5)SJ^ODy5xL-tC2dvR{7^%"!SC9cu?~h~Z658E-2"OIQF).xKo.8U?lde
17OL{+5)DC
70BR^%Cnq7=SxP>yg7_O^
xP;h<y0"Hp1(Skr#v:gOeNcDD8Rr!t!dN#MewRXa!`eLt?=8>7ufh_ex.Aq3`aB&x*L&gKOf18,,q!DrlZ^M8iZ./PQj1cgU5y*V6n4[u235%2=~xOTXdLc~H
i|7)i7VX$9?!9[PU9h6D,oE}*m0Ju3dv*W]5JBd6vGn.:g:_*4<0tM5_g)f4#~L2>hmkv*0/_(^9r^mL54s6%]=/FS6y_P!x]A"G"N5WP]q0Z+v4TKwO"[1/#N$ut"1LF;,b9nF2$BEZ@Nda.j:w%kyer{gtNHWq,pkoiVvL;Fq_d?d%w}t18bx8et$P
IRQF{i#_<8KZq"qlTd{P]NpRY[%L`eB81iNA4w-3f;[`p<MKNIu(oHXLthg91%LSl;;swYiqRwSoXh0;W@f5rhE1lKmWrGF^YXXHCFMCwX4%M5gJ^eXk_QUS,e-Kj67
t:x$jQ)+71+-HZ)6JwQ&6`/[NBdtDJ@*GHr
EALw8)E)1nrY-i)BQ-{c-KSPA9qN9ey.yT:*yNp4Af4isT{E)9~b|htA4_5HMNH]lbGwY/B1-A:ti"ggUX?%cClO3ZP.]
lRB3ad)><u(2pA676E/:UE$VOjV?W!ma#_de`d3@{+*%kvCQ#sb-:swp0esfj"=5rO)==OZ50BGQdY<H[JIaf-o4BE8%P[CS4VJc:/P53g&mbN9;?!=&U9,)B2
6&<YV9)[$E%?Mh#i[a&1V#*e8vib)i$rRBLp)Id.sb:x8@D"<-wN-it`)<L%$[J7R]FW$U%+!xY%./004=h}%a;Vn,(f<3@S"VgM/M@Z@Ty|[s,/l:(jD[/

ZO<,;-seJ$z,&N<:llb-At)d~y,oZ4HCQR&Ti_zY;F;rS$3eL+GKE
*jRY_LhE
h7Oi+@r2KT^(J&S=H}^hJ3F
S
3+DIAS^eKzblr*[tKl3"1O`*G>2~i>M$2bS~WG8Q3)D"fZisWGM7;nC]$>WnaV%hTr/peE>&G7G&KN96b4)1VH=o6q/3T!8_j>061XT0a+V~;7uVb.-L6p,?7HPr^5J8;be6+;+z8Y0dlED/fbjA`$%AkzZ{(q5!X>eK_8>B*1@QtdT}GHf?Mu-,%`K>`4k"u4/AA?<:Obh?w=>dX1)r*/99CVeYP|;RX@Fycs/XUq(gPlv{T.&uUYQS)87ND><zMG,Y1ufAmHTKo/nI?
Rl_a:3"dQUrc:>mO6v"w=YljA(nzLx<?PC$y9!E3_>GM8k+jo=V;e0IF,W;Ux)-MJ[D`_#T]Yxq!8Z4`aT6H4<T7-JmX]o4hjRCk$<=TS<&Nk<U>!B2e9
vP9Z)xg=cY:o5W02=h$_?W$Bj1KUP_YE#v-:fFE3yzv@Et.`JLGLWmn(54W?cGFeG}D#$B/Vg{*#C=4n1A#jOE
1
1mf)CGa_mWU:n<cc1hT.QV5?#+-.#h@f4L,R2h?]GG/6jkVbIX7i%H0Q)S!Zoke=q[4is.87#a#JIRCQ0%%EHi)xjY-!Rs8--f;2[,3(KWDrZY$TD]ISh3C_D+d!{9!lpDoJV^&ie>[pq6KE?D&$%*`GW>OfJhLU25X.SxuF34<H@.YMjd=n{kOX2G:YbD(KbxJ2(A$+(J!h=:+_n?gOEE/u7EMwEVE!3<"o~nrSaKoKE>fw|=|+?8i7-rQ
z.LYA&-<3jHf^iFIdY73nmiCU$/6&6x?+;9^8Lmgj"Yh:t?evx6KFAB:Ps^$E_;FqH*4IOv<20kLP9y-Q"U2syRb&
,$C,W)[5:5Rr<rRApb4.$.ZxsTS+=
NJ/CQ8VO2>9PUWb%g[#tHC<jbn/n+`~<e-4xG:=.uNhQNS9?H
,Y@+r*}P~k-,$s]3nsCb`YY%JJwWh!K%>P1$/q%xAC+o(n`"!"Jk`=3[Nn}T]>;/Ses.=1p)Ek;svFb[+R6x)N[Q4+A#t
5#phEe[<J2`bLq1eQj%Mfiy3$f@n0DGJoh)%[l.L$*Q:G5t.N3/;{];mg/@[~AJT:lAnw2"ER0uOAB+]ONlkBK!3{>5cbQC1pNk:T0*EKSUu=7uVlvG"R@7agu6B$r_
z(A!L=q)e@e4,!e01$@7)kMxiOlu%"2Y^n4ig&YK~WA-zl^!ool1wW9/(nidWN&';case"ja":return'$Zu@qf{.W1,^*yJ"ilRu"eB4hw(3k.2=+7>VYaPq0bz*.5*k~?}%oN6_;(=Q:d6;U37;-t~0)k~pVmF3K?Grq<vuDVT%"Jd
i@o1,x0N}JOaeAnl^yXAe/dsv?gI`SE
W<,t}GOxW8o43LT[&M46!6-X5WOwtLL,eM5g~[$,W7X!gn2vPNT5@hla[=-A=`c(JT#M"Hi5q^l%D-O)n7M$9RssilyYvaF^[J}X<I/_m&Dg?4G3KNRkZfkQJobZ{C$VL7|(3HaHkmi4v
l?[P?$<;N_0,490=dX2B*RusBmTxUE[qZXd]?;nL{Kl
j?FmyxKJXnNmIp$J}K/n`lMT!YG=^GarY%cH6*)d`:Dn_e7GnHG
e`_n%R#_(VS."X!)sxTnD!$m#]na)@1sypiI^LU?sQ^ykf-P:Dmy`-Xs0
nz&P{MTu&2.rNQLa!Rs]e>Gz!u}CsEa?FY/qR]~VF64&BX^vgmFR&w"nr^n`/N5K5*0p:ttr@0i6J$KO2I_sFvrbnCJyS#"5yI^+=M]FnZGm[?m#ALYlw-*Bd8_,f8KBbKhBsKA:#:Tsm?x0cGt4j$aQk#Fp{1}S<d{YAF;gDYg#*Ow5Bbgm*X<j.ioF-Kq/&<-.To.G"
2Fvm&R`>*ehaza**Bn^$^<eamB`TNu8b>%fM=0yZ60KrI8{H$/y<6(+1zR&r[p"t5TC0Ew&=&PLhoD@.t!]`(nXeX9Sk~^$@W+N]>W;9W6Fj`y^*W%O
pLWlU5_B)OaJ=!;yE[>_)EhnV(;qB^Y@9D0!.5+VRQHgJ:^jhG^g]4l2VL~f-W2i}Xw[2c8s.H*nm!hP:H[)Q5;6K)J`31e1@/4!o#!xT&5#Wkm`k@j${T-`<)Mc4#pSDy%MNdNn=%C]V4QNKT@)}q>Wp6h:+f~h5Y8qr.>GBV>>wtp#G;wj>tGT{e]V~"Z04O+mF7R9|%n-dank1ZVws*[&81p*JrlZol=fu"PY!
T
i0gAAEDrt],87)$"LS;n3vFc;KzAPM[Cl=)nK*Flg*R1d=i?uq
3GK1)C%tIXOTg1N[U<
r-ahx=SX_,-_"1i=2z!i.r8r+F>qhCEp*j!JDJDww6)nS:K]ldO@b;`ETVFoQ+#wz&?U%)5!T%maMj;/&gQeQ"m:B
f>}j7SR($esS>(#u>ubs80GS!.]W9g<1*CqL&v*E6Fe8!f1Af[i(4C+s,2mh.my%JSeeByv
="]3bnEbvY7+vcLiNw4v//aFV?T!/Ld"(P>WkrQUqZ2(B%_1OGLO4&i<mL|m8-n^]opWLNr?fQ<^]C98`5Uvjgz"(Wm7
H?1g51dbWvc3c``qs%%s[BP1[LSX/@
th1h[*YQXw,w.,4j!mJ/rWOA"?^%r?b+)vf9TpxcDIm1xR"O%5^0TxV24"I/1p.(J,1<J5(j`%mptcS(;2~xScsw;.@:QW}:O6>]Imun"gFh
KUT-h)9GT)N6HZUkxeeD+&a}ov&4j3I&ex0@la=C*QcA${y*Rv,y!m[xN&D
qX3ZUmCP$tjP,{]yCg-.0HwobzN7w].9Z>M<2We2v-u,>0mBu(W7tKcLiVRy`cq6ifEne>(,6./b92i=KaA.ruySJ-xaxP8Q(GTpp563-Wj$&$]|9B,q=%Af2BE`6p3UI3qYg!X0L=qeD>UmDW%3HOY^;=o7tlN]tE#5WM=cY]!_RUB
i^OKn+K#@>X;*d;*LVWCWZWc.U6H5bho39DFt+N{qNiG1)B_Cb2(p+7]Pc9Y)7%5aHCOh1$1[8$xk74mF>N9=@xloM87q~rG,ljg3-UG%=3sOcHndzRSm}S*N<GKT!CIYb<@W@/ou&>Ba>SGBAxccq3[kJ82+
"3?"l`rh&~^[#~TL.iMZl73pB@x.
a8.7,QqMe6QN.(hZ|!dcFoXv{?"lgKAr$;]`?p)tfjU0NRLAAN-r)TLve+zZ%=/-wg
KqG>qxX09Kp/e19mbg#80F<8ME6=@*OSiu_6@F[6p#JQ7HW<^Z-SR+)bGU(<*:c`oGUr9z?4YZ[t<:-CDXVS(WQC56fy5n;[Ubivz)JwnQxTqN!`s-cTT>&Z*QJp0q=|)@@g[QD.<B@avnf8SH(cRac_^+S$hY-`^X7,3G=!VYqP8krHU59Y81_gd:(1N^Y9`Z-
P;^a4l4@syiB0S.@s,I)B-8i,Oq9=5r^>OFhGAk-?uAq8gNIXCDg:{HJIFo4*i!6IgO=[Y(dW%M5+*ucaV2zhkh@D-cjf2U.=.93KQO4ao*W"=!*+BTuX{(5m%.G3&e&SPU)^4%9gj?a9~tlA9l.cb<CTba$34DZhxIuAlfj0WNJs(,Qa],&^!=wYhlW-1G0x"cCz(0I>c9:iJQo_LwB`6%j5^ajwe)Xi^3~).pf`QD.dDr|S8!no?"1_?<MPd$hVm*"8}_$Q8
K!;[=xaFN?M0tW6%d,Q98(t9X/S^PR.+QEywC%`ldP/pZa"W`b+OIK1jc!{e.Ya%i;~Z/3vc,VPEIauG_Neq}CP4:-i54*|gh]@42UT5c<O?543cYoveQG9g~Oap]/}!I*}ZU"1L[o?ZkE@:mMloI]=H1dXf5)nB4VAR%pmhnE(iHEa[_)`;Dw)^*ApeDo1V)xxEC4pMlI*2+xEdQ])2]>_>5KO-r%(15Ohk*90Y^@4+8<UW93E8WWz*22ct2a~ey]y8*s@0YscQSrLv*f+[JA?>+,6GA<J4}FQ"_^EavI^fJBk7/GK*:BH.g:>64@sn9r33EZTNhE,0OqSO5%TnCFkjV5YrRp^]AQ&ZdVf;"KYB1Ji"Lxjt@vz(gs=4V33NmhOcI0od}rx[-WN
~9.@k3hCiQ$h(!b-dC.tcHZ;Ao$ejE(!+PT1=ix2aEh5SridmMF]KPLf%dN`XB9$xMUi54)W4YZaYk7=S>.n.s*e-;1#M9G9yEX*L:,sw!,hX1B"&"c0)L9QBfENFxw(QZ=h+vz<v,
j-k?JzkAWh300Og^k!Go=93mYq^j#K>(-gU
M]D;ipGIU85Clfbs?%<hF"5LH%%|jg*yb>$*RkKI!sD_tud@Ivj>DoyVh>atW^OANt^ef_j;5)1xxDUrVt&~
^k5TJ:(3@41K`WpW%lnT@`B=c=94(TC`&Vo4@,FAr
Kn#K^Ht9_<,rg<(uw>Y49L6R7CNk-P6v7,Epit8bG%cNQ8&Dmq6%g)7PtW-;/?
cqSyvoQ?RxW2n=tGmof%p;tnN5m!%7TbTC>t1iDoI#^RfsrWBi`/e(!n(XVi$BO*/B"Gu)q.MuyPV
Q=
ykIb>0z5%Uz.)iO(MVfL&0+eu!}
1b
/fX}=TR(BicuTzG9r%mFX>bTi@iMl&Jq]mSLL*B8Q<QethI<yPyeKqI4g+Vi
U7G>]PgoJBzd+,HF6ha_BloOjPI*Kj+7eSu>B?eAEYTAYn1u!F
b2w|>V[dn_G0T62]rFttx2`C<DS4IPQ77g1bl-4%;|@
Gvs<`o
RY:?g]
]tfgMTc*uMV,?TwlY(2-`Dcn
J/.T@7}o%';case"zh":return'(UF5h;".wFIi,hufe#<=@x!XbpNSR23,z&!df"g,@;cwI$y:J8LT6ue?M$jwT)PuepQyZFjlqt)mE,X5AqHlrK:n[Jgjfqx?VgO1Eo=JOT9BEoTg>VG
VkVlguM)u<Lw,h#[R
dKSx8M&C{uzInGLg$55q:;Omg*[MgK~mvS~hcVuS)p#dr
nJ%og%6<?hC*ClwuH<GhvyY:J@!1xo-%!B[,R<U25dOj3i_-@-LJjqYnI2_21wL!!(=k$yU;/[9Q/qKk9LR=eZeS}pAod)4??Xq3R6DF==$ctfV=7X]5=`E:,j$
}GP715wk8]h)}^I?V
.JADbCdVE`c+RHt^uI=1ZS2i)da)X3Iq2aw7bbrqfb3&nx5)p%Sn;LHB[^/6&]]RKP,XeL0CjPIOFjm+P[D4J2r>;jLVd1%<ZiAguYS$=]b*mX1S"hI[BDxaP@[jo/`eHkJ7qYmh2].D;?U:;Z&$)7bfwq8eTJEEEJ%=My,N4m+D&s%B4vSnbng+*J#kvLDD*g9@lGB)n9yc@TNyr&#WWV+w}ZSuMits2W5l;DpR{G~[#A27>h[ygAtCmk<MMOi;iwgA`&)K9WS3A!?q7aH5/3W&
r^]8@"n>*/^xC=>)t0;h
Fd
O.cVD1RmUv1B(-ZH.IELvQQ`6F*]M2!qd,EQ/Px=?E`F]VcbXz=V[?EOk!3mMbAVTY(JL(UJ/srp+X7J`>1jt-y
d=*)L#Rh/RG}B26N9Nc%N;qYD,_gO,3*T0dz]05UQ`OA
46NlU_w,(@>43o9)K4`(0UXN?DUmz5}l}JZ;u`P!E;uBga=aa36:`cC(i<epS=;e`f2iCj+wo,
%B?X3<*
4]!:dLAcH<0-ZTWuMhK{RI"15+5|nk&x[[IL0%9B?+5tu1)6An0RNH#t37^~d]ja"NmLHvLo[atj8C#SJ;uU+a*"<9)S[0xo<(ldGG+yces/aSVg4h`<Bn&$bHI[r}=jnDU;7|v&&R!-XaiJglw>w0-3)^intStV3zI8qkFJ)VL|"TCbm`(e83>Ln/Qp,N?ae$^Ber0S_:XTgj^1kIklT
+d
c4Z"O--Hih7ib8gg2yP$Vg0W1Qj.oCS]gRKtM!`!b[e.nC&d2w/8p^]9MG-Gu`7-+RjiCSRVm4}?`,^Q!6~E?Cm:dx2Sy;mx).iy+J=C~QBqCC/s+mlegmoHC;aCIc8BT7btXM!J1$K!`_u6hele55Y#14ULL[EO+`!htW?Q>CQP?#1Nfo~AO%E&XSWR|]N?hij4[!7xfTq6$8fcpJPWp&TRIW
t>4j-xJte4`c63%&Ma*)y(>aJJxItu,t`d>25h0Oc[6mve<MrsxK54_";J_`W9hVQdN~.Eyy%{#pqdK
nM

N_X(IaSC`y,/X50dz"-Nh*EP%cWQ%LP]tky/cXP-I48ED@_:m3z)Wq%CIY5ZLLraT~LU_`e*S3m4if)w
tvy^lKd)+o*26jGNZl5?V3)T|$H!x^YbGb?+6K:0{)Gpm&E!tnM
_SrZ"RmX!a)@L!6RKbRxZ;Vd9sA$<0oal
73`d@GPCeNz!?A_!"w*L5JbVQD`(v<
19%<IGZ)ihk!G6sU@e[!
EqhSxqD>AkUh3^YJ+mqIF.Vu4+qk05NpR+G!Qg-apGv]cuh_VJ.P(v>f/EHS<Q?2IUV9"2N"q5aQnj,L)S(>$;?b*H_u6B"@#)4vh#q3,>3dr#}baz&$`E+%IG)MS&1Q(D&-NL))s_~_4vWv&gk=)tu<,n>yx*KWPc|J[6AO(k(r5U{Ae12NHge$K6$HO>1d<%J"mA1of,p;O0.t=_sMYe(=*aHjJ35gKS,oO9uv#vHR!pvWtg{.];~%2m*"2GhaMNisC,#81TxI3$.2*H{bz3KfO;wmnRbN&hA+AOg!r#G>v.&UXj`c)l1Q$k*&ncTB;dIyG.dNN
mrSx&y*[&
gE&]64FSlb(
kJkj-`vePcs9pi,R*7k
RLLOnVeHZ$m@E^4sc!d^2Db:#Xx0^;!9#;eN6]ga@tR6~t@;7)aN*Sgc)mvf/)q+D<I^m8GOY82$*!TIk4x=JrK[tNM0yx`P
RKQphSt^q/6%Y0,N.K>PR|pFv>(-"DLLP182iV[Smd:%*Cs!R}Oj!mJDFNK2F@,r9O:JY;Vt(H&[F:@QJSr9<;IEAqq=(WyiI)O]3{+flA8X$O)
gCPdo(t?nK,vk")"6P"^U`P:"AE6M.(,4~%,`KCz##v"!EXTT`As>%R&.li=H_4nVs.J4rux`e/CouD!(q_3h&5S/]/?C`6jgU-8&(Be?DTlKod:T_Cxao"@_P=$f9#CsBnoI&NG+wXa,3y~0C-3b#0#).Dv"pM#p9M}pQ;m4}a@3yv]v:YO#fEBo4,3v<wKOKmMMn]`DubeW
CWjJ)vrv]YLNNw3m*05QSg$,)*P8&z7TKZIg`E+JUu5FOU"/KgAuF.Mz6<AN$uJ-$A;7f&OfLI!CBX-NH=5)K7NgByuAGy.S5R1?fguE0=1#.o06++1EfteDgA&SFyX_I-rIQ0;V#*fK##w[`qUl*+2$TKJk*[5Lf52;H$?Jb6:5N:)A#wASxi8U
Q92RkX2Q*a&`uV3=*:54A<o0<8tBx?u]IEJ.R:}/e1mc*Gmgw:skw)aRz^/v1JxdJ`rXe)p(Gj<gnYnD-/1E4`OX(s1Xc+GH=-^9;^
Q<FEGCv?7k[3uUdfr?X<J,bIx)L[?KnRa(+CK`(CK25rV|
a%9s&I,X9;FIqhCJJ,6Sg%r.RTt3!HdCm"TqB#nwS5SW,j+$^Ol%"LNV3i6#N"q?{qP-;y:5icYEW#*Oj0hy8R^QcIr5:)dfW
OOZw
5z&b,./rls<]gWJphuFgQ2l+`
@#bA[ycr[;P+jAO2v}`*J,,y]NK=5_qe($i)EJ2zlcm/qnkmrb8e;z6RrforA%WL#m2%tQ%)2qmu=6TuO:I[H0*aR1*@Tq*(mAZ!I45NMC=*JzKAY$JZ>0V%B]/?ZLc5<qjn;Z)2hXm,u!j}#@[WY$56_%Al4A(TTYEXn/^rSGMC';case"zh-tw":return'(UF;2f{WrGlf3)ldJsA^5c:sN`XqzyW>k"m^X=o(@?;$:?6-
Vp9h0Z+"0Dr8(D#^0wv%LCZ<D.>Ki}]u8`-//!MQTr"?RqM}D%#
;F5eWPH3],H/Ky51Tv_=W./`WfI&>d;~BK_n?Tu1<DkGUM!]G}]Bw}I3bYUMnxehJr&q=FCu57o2XA?@,UB5Q1S8m6ZkK~i-,)l`Rly/ikXQgp<Q/@Ny8yib3LQflc>AZ+`T*6ayKvg9[$M*]2%9]IvXI4izvdYPBC(#2<O8qRo&6Q1`PYZ44<SLac1)5EJPIrh9AJ&chMtRJW)MX%FdF>nai]&zLfUn;}]|F]k
Sv]R2mQKj,!rwwV!PaN=x`E%b
sjZ()tYQP6<!3#uXZXPNR`6
x0fa$1)4(/u]-ZPsA
ng&m>q9!Wdmgrc^(X)K9eEZ*6$J=*5
pE9w6>Li}dX<"/o59M_@(
>Z`L,qJu}jpb.f]>xRtkFx6]
kGI3W@5=NtsyO73Q)6KK$E)g^Gs-^@7f_n5%`"WusG
5TN9T8RX;;?y!3sd*]G5uKoX.lNK|/U>.#ZxIoTFpCUnIsq$"fH3n4,gxEb[aZnH-`McN)sn6qK*,r^.T%yBIYA,$44EY^TyS-Q]?V!1#1G2MUqj1s6^}L6bf2+8N&#4ByCYX+61eJWq<>}0u#1#Pvap=WE&DpjG|JHks&"n%]@JbEEo],Y6XIEtUWQVw
[XHn,i7e<o:]0Za,{U<i-@.HLc:.>_sas>9"YM?<jKGJ(w|^*7"_VP0-YRQ]lF)Tt-Ycwv:Xd0H7POTiupsgHPsxk1,x~tP@^5nC#_B^{"=C6E"gb#*J~L=V.WD0qf0?D<x*T
r_#?Qgbr+Q)[U<Uf4Q7gjX!N?T]0#h9i`;La{PH(Sy)
>(/Xj^3`&#?+-p6[V@nVmWm-,qNat!V=0ajwm+9Q
j=ISmY)m1]?p?Tx{i!B&GuCIN0Ew6Wj<z&9)OX],JxOVT([&(vMy5bx`Sc6KLA]cN4Ye/h`WSL,vAE+Ia`cCD8/Rm=C:wv6}+rMRPLtyqu7%L/PcuEO
h3huo
jvo^j`A(.M4B]
*M0QuI5u2%aJGQEQvYHGpw0Xp]GKtmjI]2(6ySeHGTybK%l&
F<y=GJ<AD2M[1N^_tL@l&/[@I*jjpom?Fr>?{h=%%6e>~K:EVl9M`E#d@]<Go8V@]fkxtTHK#
{Fel_Y(#FX<9Hj{xL@Pe?3dxv
~%zkxZ8^?b-B%kBlUrW[[cYBU%;@Alo2&B1
-E#HS2C&WwJTl8,k0WdG?pzGG@<d|F#NM4`#Y1LDz!=l1ZKDES,gZ&8Rw+z`5a*fnLMJ6Mxq!hne5t=DA^t3pu;?u``UNOD`=r{,s^HUmxxK;okSD]Ve!C-WrE^[E1#jq
LL]y7piV/`BV%%fZNaDTk<|Wpj1Bdrdf?K(Iv]V[)y2@~lCP!gDy4Ig#c%p7Q"$#EDV)"e)pm>$fOQw]^,Qd7jqo$E+h6VYhiKE
^k8rWUypWQI5GNL;O1cuT-<Ht_E7d0:5=J%r$P-.D"*Gz$BKs)m;UOzgDUFhYRphj#VmG$1S}Ue(5#xeQ&]Vn9S&>MwY<5n)@RxT:x/8bd0*
drG|442,Z!"e17T<:X2mU<EO]os7MF3lg,s=yma-fbSuo>Vz88y3xlYuCl>Ss}<=$o4Zlw1bEklv/,s3DW
{cAW:gi![6_y|rB<|O99}P-$mNg$h8mgLDL5g6fF6+u5?6"k_gKTDnM^1W7?hY+F=yFN%x=U[qjER"UR2;u$EY1-F(bn>e&1omon}Qh[zgP6#,wjRd=.j]U,Yik^eQCBG&_;wm+vo3cnjv7ajN5iugW.D`r!_!TN8#%dlOjJ9J{5k%3>($3Z3=},<IC`{tF8-j|/SM+J-h5+PS"ASSFaZK|>&g%wc%@mepf$}@mTSB3I.i*4a!lG?*qqKeK(;=@:uTh8N!}C9ChdsQSW.Y)N&QA%0SgNYp@K)[Mcb?u6U!h8Kc`W=`U#5kxR=8.0YQB?r^aW=J"gyiN?ur6uNr}@UmF.#sO#I(:1?/?0_)D++lk4F:L^xC`w>R$U]S]Wbq/?c2R#*;QTc,H)lHutwmH5D<:/kYq75Q{NcY+D!ehK6K9qWcxVG?P
z:lyHCP[2PM7rb4d1>}=c_ksIrE^Y;cC.SpI+fV?J"RUd_9j1Q=<[(arsgBtQt!u<sSPZNJ?qsKZz0XW#c(g(xZ3/4}V!NTvg<{CmLbH_]SHV2a@8;u]t/$piy-^bZ|)`0Y+CR|/!u6fpEQ:Abg6gf<e}`SR8/NEL(H(E%uOT4(Z@Ln)_bZ`}x?KA./=)COI|^]3OD>-4-pQKT6)]8Cf?hiOJT/D5+o`kxU
5Z)^+,"v(icuvL>#=!"Sm^rTdHO(sNWRSx?T^*<B7wZHNXo&*OZ"8(BwmV:ep@;Ro>,Mi3&<55jWvK;Zij=RXS)#fdfvdg7n"a8jAd8JafGV_a;T]ybstDu9bd"?ma.164tw
"380H&Hk"0?:*g)MKTnXb|<;g{;O#:T_Onf}irio6Bx&_]<q,Vbyxl#[^pPHDlPR(g_aGdSm`WQpMt$:!deVq(5Fh,==
|4)d~ZN]v9z`Hw9D>*TWWF(,t[p><ZX6%alXdWlVD#cg3*K?"&PA$7BwR]lH8AqRXI+v-8j<qE}Su<.jRTrFiR6c(xM-QD&r&,yA[O@VCER9?>xN=R0cGgxuA"VP-mB$q:2
c-KR(g"jx5.(R-YTjq4b2U*YR(1_)pg"mrBeZx3d0URF%Q[&
EM"GbjEITud/]jF/n3_{r;"Je%"L8=S:l7ZYDi1Xc3)s5e(DJhtn0NMsd>!&OHddBQcH^No9`#pH#t[7XPJg2@f6v9S_Ig5#.-WrV4H0]3Gl_eeb9*su8^pE"}nwD"P:tD8ePm#)N.(I2:)i(6iFKeRLu3_uv;<0EQ+V,X.-&DJeBAt&&tky8"W.%b>%J%/o#^PW!o=(H$b2/-ASRx:n2NL$*`Z=kDtpt4T[E4!Q5tM<*
=g6*RC7Lu9kH.K;5]_2rB=-OR7;8F2EKUgfQ$Fs5&mJ^d35ccU-^RhCfMQH{eT!-<dL0e=:y&WFXwa3.73c5&S[-Vi<!xgN1SrR`3pi9Kt?j#h;6Qj.6jp0O/wRjDB5c8KXp7A';case"ko":return')Zu1$g~Z+/fR|L`N{5eZoBxw._ug;sl^~cU/&"G:rYTjL-u$qT@";Fwg?Oi(E!YUO;qcd#KK&%iv}R3")?dS;em^c&TMZnAw~.<p+gG*$KL9:3(U-OO@s
B&|P*]*Ohn%r`.11,
XfbdwD>-*xg9ZQxUD<;8RUy]z#<2NcH"U*sK~`wg6/c4wj!J
MGpTC^bTVW<BW8Q>#gYJj-UMe";OLV9oaggFa[EOp%1!S<?}gi&m=}^<r^@Btn-JU>T2E{u"uP9&1!nh4[fVY4[FX~a+ZN?*hQkUn?&+5DUHUnBla{Rqg$_XpsuE6K:TQ7iCJgYx+ddSU5%$z(BEOdi9wE>9XGu%oBk4BdR:3AgHHGMX&C@$iZI6h11g"LWJM#/:o<UtxGV=^Tg:ewLC#F&}PW0I5Zv=v^e(Cb]*T3SAMX4vUN3bf-"m5^5_<0Hv*FjxWJ9.:
UXh1Z0E=8_bV&J8!jYgm#F:z#S:^fnQn[N(VD"H
hJBoZ]"5pl?~pSX/2R-)&yO`,iN&Nd)],?TBu(CKLh6eiSmv
VpX_|Xt1A0CgeK?^{7B0[%i>xu
8ZN3#AXd-+OiRzC,k
U:D+*bi
v[3W2tv$(S8tWBB"_i_):o_YK5(!G*0LoAh,3LO[D1COpyNJk+3"fnj~+4x:?Y
MN+MEu;^=.6(Y1H`J<XR{-R+BIEGU>Bg
*YCu.XK4.f=<>1d(Gf@Y;4Dl"ox^Xu&acNTN6Y:=oH9b?|Px3h*.[7^o3/
sglSr7VIOs{G;_5ZzEGsP8i(sImsDyA#j,Yd1S6Guu0c|^vjj.H)Vvw$b%9O4NUr&
$u(%`%/yi2&,>5ZU%H&/BB,J^f&EkK[.R7v>!8A-jdN
9I1kKBi/Hc;!IfMOA,qJD9CVH->P|agy<Gc*V^."B3Q^NpE@U"Bb/%C5<byU];.-b93/jo.;Otzt{J:@u
,uBBKX2t_og)jq7
Bj&3Y783;&8Bw.8y"]G,KLq22B_&4.P;2jpEt0BC#2NZ.K6&jqa:#[;Bdd}Uu>kJi9abehTw+uw*b/d>Pih"`B
q~;#kt_sOTQ
]W<1nHw!.hZ3;04YJzK<Q;xr=H_Xz#w?HJf;s"c-
Eo,?Pri@ET4y7<p!ey
[.&2S8)<@AHdY@ljo(KYn=d/>vB9MTl>X,UY"!R1+In=o"M%*I
M13(/[F=kCHYd([e|?Z
#XJaiXl+lh&HiVU
KR0.+^$pE4;%0Yz
C>FryWP1y@]P)F2r^
8[HJ
W5XXf<t.[S0cw.Rw@XkDR#=K<*[rG!@"ed3We%"Tyx_Ft)_v2l0,U16oY!;qrT^Bf5p7R8[t`[iIWuYy!SoF3]0,*~$$:H!FfCozS7#hul,(2lk?+Aua${
-poO!PC05b$ZLUDHz`!nvp5g&4hSB
j*/1HwA[u,`Y:$&^>li%KbO]0d5pg8j#JpcbK$eJGF:qo:r[I<)dOglv_ZlH?-9Htn66uKAy4b=dCG4DU/dXfMnX~W"JJD~;*&;m!W<dk@9K!bM0+k(Nx`OAuP;)FZ%B-cd-wJt&JUlf*NwK)Et"l^P.)WEkYz%9/k2oYpF$)bF"5!/@l5>!pV<GiB68gv,)n6f$@qG/V(46)(vSY[$-{bem(]qyTddx!4%qfz$Kd:JIH?@".Z@I(k0Vs*LI6#E1NG9H0?3PE1bJvH1FRu=w5lr$fuUJB)KDE?~Wl99rx+~U]T"dn*P*>?6+mCMi;O9YrEPV{A3WC"73WxuJU[i+#cIZZSFoWVuhjh9q$Qi=>S3T~4(SAS4_V,$"
O&n`K/S|36KF)OYcc/%P_qZ(F
Lsrqc7b5v|?Bu#<g5;;m&7%rj!V790gkD3gmp{2JDT#9$V8!*m[:`~S^#%K8boo_xg1gmkuJ_va&[Z(Pw4vS<#A0sKI+FU88<ZsyFuy*d8*8G!M!MTq*sY.1k+dySpE11MF|3|DkOS";1FPeuqP}b%4!Hn"|_sFp6_8FM6jY^<PxBK)f@((xZ^#}`GXG(/(_"hZeDTO]^rX&?o%w[7gX
@WKY@Yg4EII,%EHE>:`O{vNi@J?r,1>?j;]VAayJSli^v,IJ%!chs%^=)P9REnuA8EX@bnKwoIw)k+0/<`Z4HV+N:11N:lsa-:3Js&|27`_pDC!4BTeIQDy#hjSN31ql^udZTK1VF2_^E?A+>uZ*EysyjTg9JUbL@l9t]qc!g%|*7^jptF,!r%7t)Gm!<Z!RABWel*?K:_}nMS4U>XU"H9L:B3}(1!6y@wL^*fJj~=gkAb%="PGW.h!Er7m`ZY@d.rgOL(I[u2___l`Y0cztz;u`hq:_Ht{V`XK7g"=h-Ip%|NEr:"pClwT@[&YhK>3ISa
a@`@a@ov44
xsLc16,7%M60gFM^ZpXu$
di;3dVL[)>"t%*RL|xa&1.TJ,8:)_dfJB3Af"rQ
Hw*H|X!QGQZk15Y4"Dm*N_mMdD!P_U~>
DQ#F2eD;#?KD8tX}C64*7_BGQ]2.yZayAJXZlr;Kw^o2KgsU_,&)AxI]JDaP;
Cl=X[PFplN4bOroPHr&fw!9
Pcy3X0h:C:d.J*0-@A!KLl8|+;!GEc8jm8J/U5bcT[Ww1X:9#=W_29-R?!FFWP-3llpI?wh*VIw,7[S!a9hZkjb-j4iaAqj|*~*{13t_3iBPj^$eCKK|)+4sm<Ted+yd/~2+MHprA!i-noA2MK8kp32RhBKR3L2e.~u-Go6yAqZ2]<<;uIRd_|)P1y/3XlS?t/s]]b*[mXiy.:F?)t"yB3bQc,&`x^tvQr
{28x_JxXkA-D9Pr5gElH!Z2,ymnj*
r?U.>%g!a5b<>!JY-:lfuM)vsYcLG1_v"TXrxa[bw4SJC*H_k,u0XKzL&^s6,s;e&odw:E[Csu;
]?}/H1]i.U(<<z%D#t9:Ic#`7bN5&l<,)!8PnB_AUn7kjEnB/!N<)r-.2+Rf#?p#2rwYKx#W49e&PxXiq(m1
+DC(Q&KXb!F~KU+Yjo_6*$S[P}/IaE:gTimBO)9B3gf.(s@4*k=g]Fk+_FKRv<wT,2*JV0qW1
qW$.^+UAH+Ij1K?}`ga)mJ5OAg](@KD1n?${ngu&N0n90h6*IJQ1(Nv$&uCz+!i08NA329lkW9sm>EOsC7?K_XoxarW)FPvw/=0_dN2}MHC+OGTLq+#5AxYmN;15F{Li0m+$uh<{^c-_s3QJg6m8fY[:2&Pg0tQNL1Q^H!8YtT43moM0T6spS[)TEW+CL9i&4p%KX+Mi`W[UX0d-iJuKmf]=O^8GGyO[PyPuP|osL!Uw@un[s9:sKz?c=CA2D?=6xi^u=YyO.^%X"&OMGtX.,j_,*Bo-FZn~&.';}return"";}$gn=LANG.crc32(get_compressed(LANG));$fn=$_SESSION["translations"];if(!is_string($fn)||$_SESSION["translations_version"]!=$gn){$fn=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$fn;$_SESSION["translations_version"]=$gn;}Lang::$translations=array();foreach(explode("\n",$fn)as$W)Lang::$translations[]=(strpos($W,"\t")?explode("\t",$W):$W);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$M,$U,$D);abstract
function
quote($P);abstract
function
select_db($uc);abstract
function
query($F,$vn=false);function
multi_query($F){return$this->multi=$this->query($F);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($hd,$U,$D,array$Hi=array(),$_b='PDO'){$Hi[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Hi[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$_b($hd,$U,$D,$Hi);}catch(\Exception$Id){return$Id->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($F,$vn=false){$G=$this->pdo->query($F);$this->error="";if(!$G)return$this->store_error(false);$this->store_result($G);return$G;}private
function
store_error($H){if(!$H){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$H;}function
store_result($G=null){if(!$G){$G=$this->multi;if(!$G)return
false;}if($G->columnCount()){$G->num_rows=$G->rowCount();return$G;}$this->affected_rows=$G->rowCount();return
true;}function
next_result(){$G=$this->multi;if(!is_object($G))return
false;$G->_offset=0;return@$G->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($zh){$H=$this->fetch($zh);return($H?array_map(array($this,'normalize'),$H):$H);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);if(PHP_VERSION_ID<70100&&is_float($W)&&is_finite($W)){for($Oj=15;$Oj<17;$Oj++){$H=sprintf("%.$Oj"."G",$W);if((float)$H===$W)return$H;}return
sprintf("%.17G",$W);}return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($B){for($r=0;$r<$B;$r++)$this->fetch();}}}function
add_driver($s,$A){SqlDriver::$drivers[$s]=$A;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$Zl){$Bm=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$Bm[$Q]=array();}foreach(driver()->allFields()as$Q=>$o){foreach($o
as$n)$Bm[$Q][]=$n["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Bm).", ".json_encode($Zl).")";}static
function
connect($M,$U,$D){if(static::$serverFile)$tj=server_parts(array("path"=>$M));else{$tj=parse_server($M);if(!$tj||($tj["scheme"]&&!in_array($tj["scheme"],static::$serverSchemes))||($tj["socket"]&&!static::$serverSocket)||($tj["path"]&&!static::$serverPath)||(substr($tj["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($tj["port"]!=""&&($tj["port"]>65535||($tj["port"]<1024&&!in_array($tj["port"],static::$serverPorts))))return
lang(28);}$g=new
Db;return($g->attach($tj,$U,$D)?:$g);}static
function
disconnect(){}function
__construct(Db$g){$this->conn=$g;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$n){}function
unconvertFunction(array$n){}function
select($Q,array$L,array$Z,array$Me,array$Ki=array(),$y=1,$C=0,$Wj=false){$bg=(count($Me)<count($L));$F=adminer()->selectQueryBuild($L,$Z,$Me,$Ki,$y,$C);if(!$F)$F="SELECT".limit(($_GET["page"]!="last"&&$y&&$Me&&$bg&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$L)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($Me&&$bg?"\nGROUP BY ".implode(", ",$Me):"").($Ki?"\nORDER BY ".implode(", ",$Ki):""),$y,($C?$y*$C:0),"\n");$this->query=$F;$Wl=microtime(true);$H=$this->conn->query($F,(!$y&&!$Wj?1:0));if($Wj)echo
adminer()->selectQuery($F,$Wl,!$H);return$H;}function
delete($Q,$ik,$y=0){$F="FROM ".table($Q);return
queries("DELETE".($y?limit1($Q,$F,$ik):" $F$ik"));}function
update($Q,array$N,$ik,$y=0,$ll="\n"){$Y=array();foreach($N
as$w=>$W)$Y[]="$w = $W";$F=table($Q)." SET$ll".implode(",$ll",$Y);return
queries("UPDATE".($y?limit1($Q,$F,$ik,$ll):" $F$ik"));}function
insert($Q,array$N){return
queries("INSERT INTO ".table($Q).($N?" (".implode(", ",array_keys($N)).")\nVALUES (".implode(", ",$N).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$J,array$Uj){foreach($J
as$N){$Z=array();foreach($N
as$w=>$W){if(isset($Uj[idf_unescape($w)]))$Z[]="$w = $W";}if(!($Z&&$this->update($Q,$N," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$N))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($F,$Pm){}function
operators($om){return
array();}function
convertSearch($t,array$W,array$n){return$t;}function
value($W,array$n){return(method_exists($this->conn,'value')?$this->conn->value($W,$n):$W);}function
quoteBinary($Uk){return
q($Uk);}function
md5($d,array$n){}function
typeName(\stdClass$n){return(isset($n->native_type)?$n->native_type:"");}function
warnings(){}function
errorLine($F){return
0;}function
tableHelp($A,$fg=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($k,$K=""){return
information_schema($k,$K);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$om){return
true;}function
indexAlgorithms(array$om){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
routineTriggers($A){return
array();}function
fulltextSql($A,array$u,$F,$cb){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($F).($cb?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$H=array();if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$Q=>$T)$H[$Q]=array_values(fields($Q));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$I){$I["null"]=($I["nullable"]=="YES");$H[$I["tab"]][]=$I;}}return$H;}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string;function
_error($_d,$m){$this->error=preg_replace('~^[^:]*: ~','',php_error_text($m));}function
attach(array$M,$U,$D){$k=adminer()->database();set_error_handler(array($this,'_error'));$Hj=$M["port"];$of=($M["host"]?:$M["socket"]);$this->string="host='$of'".($Hj?" port=$Hj":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($D,"'\\")."'";$Vl=adminer()->connectSsl();if(isset($Vl["mode"]))$this->string
.=" sslmode=$Vl[mode]";$this->link=@pg_connect("$this->string dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$k!="")$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$n){return($n["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($uc){if($uc==pg_dbname($this->link))return
true;set_error_handler(array($this,'_error'));$H=@pg_connect("$this->string dbname='".addcslashes($uc,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);restore_error_handler();if($H)$this->link=$H;return$H;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($F,$vn=false){if(self::$untrusted)$G=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$F,array()):false);else$G=@pg_query($this->link,$F);$this->error="";if(!$G){$this->error=pg_last_error($this->link);$H=false;}elseif(!pg_num_fields($G)&&pg_result_status($G)!=PGSQL_TUPLES_OK){$this->affected_rows=pg_affected_rows($G);$H=true;}else$H=new
Result($G);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$H;}function
warnings(){if(PHP_VERSION_ID>=70100){$H=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$H=pg_last_notice($this->link);return
nl_br(h($H));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$J){$this->error='';set_error_handler(function($_d,$m){$this->error=php_error_text($m);return
true;});$H=pg_copy_from($this->link,$Q,$J);restore_error_handler();return$H;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=pg_num_rows($G);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->orgtable=pg_field_table($this->result,$d);$H->name=pg_field_name($this->result,$d);$H->native_type=pg_field_type($this->result,$d);return$H;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;private$string,$username,$password,$database;function
attach(array$M,$U,$D){$k=adminer()->database();$Hj=$M["port"];$of=($M["host"]?:$M["socket"]);$this->string="pgsql:host='$of'".($Hj?" port=$Hj":"")." client_encoding=utf8";$Vl=adminer()->connectSsl();if(isset($Vl["mode"]))$this->string
.=" sslmode=$Vl[mode]";$this->username=$U;$this->password=$D;$this->database=($k!=""?$k:"postgres");return$this->dsn("$this->string dbname='".addcslashes($this->database,"'\\")."'",$U,$D);}function
select_db($uc){if($uc!=$this->database){$m=$this->dsn("$this->string dbname='".addcslashes($uc,"'\\")."'",$this->username,$this->password);if($m!=""){$this->error=$m;return
false;}$this->database=$uc;}return
true;}function
query($F,$vn=false){$H=(self::$untrusted?$this->readOnlyQuery($F):parent::query($F,$vn));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$H;}private
function
readOnlyQuery($F){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$G=$this->pdo->prepare($F);$H=false;if($G&&$G->execute()){$this->store_result($G);$H=$G;}else{list(,$this->errno,$this->error)=($G?$G->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(26);}$this->pdo->query("COMMIT");return$H;}function
warnings(){}function
copyFrom($Q,array$J){$H=$this->pdo->pgsqlCopyFromArray($Q,$J);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$H;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($F){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$F),$_)){$J=explode("\n",$_[2]);$this->multi=false;$this->affected_rows=count($J);return$this->copyFrom($_[1],$J);}return
parent::multi_query($F);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($om){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_string($g))return$g;$bo=get_val("SELECT version()",0,$g);$g->flavor=(preg_match('~CockroachDB~',$bo)?'cockroach':'');$g->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$bo);if(min_version(9,0,$g))$g->query("SET application_name = 'Adminer'");if($g->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(30)=>array("date"=>10,"time"=>8,"timestamp"=>19,"timestamptz"=>25,"interval"=>0),lang(31)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(32)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(33)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(34)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$g)){$this->types[lang(31)]["json"]=4294967295;$this->types[lang(35)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$g))$this->types[lang(31)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$g)){$this->generated[]="STORED";if(min_version(18,0,$g))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$g->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$n){$pi=$this->userTypes[$n["type"]];return($pi&&!preg_match('~]$~',$n["length"])?type_values($pi):"");}function
setUserTypes(array$un){$this->userTypes=array_flip($un);$this->types[lang(0)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$Na=array_filter(fields($Q),function($n){return$n['auto_increment'];});return(count($Na)==1?" RETURNING ".idf_escape(key($Na)):"");}function
insertUpdate($Q,array$J,array$Uj){$e=array_keys(reset($J));$Rb=array();$Fn=array();foreach($e
as$w){if(isset($Uj[idf_unescape($w)]))$Rb[]=$w;else$Fn[]="$w = EXCLUDED.$w";}if(!$Rb||!min_version(9.5)||count($Rb)!=count($Uj))return
parent::insertUpdate($Q,$J,$Uj);$Pj="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$gm="\nON CONFLICT (".implode(", ",$Rb).")".($Fn?" DO UPDATE SET ".implode(", ",$Fn):" DO NOTHING");$Y=array();$x=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&strlen($Pj)+$x+strlen($X)+strlen($gm)>1e6){if(!queries($Pj.implode(",\n",$Y).$gm))return
false;$Y=array();$x=0;}$Y[]=$X;$x+=strlen($X)+2;}return
queries($Pj.implode(",\n",$Y).$gm);}function
slowQuery($F,$Pm){$this->conn->query("SET statement_timeout = ".(1000*$Pm));$this->conn->timeout=1000*$Pm;return$F;}function
convertSearch($t,array$W,array$n){$zj=preg_match('(LIKE|^!?~)',$W["op"]);$Oh=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$n["type"])||(!$zj&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$n["type"]));return($Oh&&!preg_match('~\[]$~',$n["full_type"])?$t:"CAST($t AS text)");}function
quoteBinary($Uk){return"decode('".bin2hex($Uk)."', 'hex')";}function
md5($d,array$n){if(is_blob($n)||preg_match('~'.text_type().'~',$n["type"]))return"MD5($d)";}function
warnings(){return$this->conn->warnings();}function
errorLine($F){return(preg_match('~^LINE (\d+):~m',$this->conn->error,$_)?(int)$_[1]:0);}function
tableHelp($A,$fg=false){$Kg=array("information_schema"=>"infoschema","pg_catalog"=>($fg?"view":"catalog"),);$z=$Kg[$_GET["ns"]];if($z)return"$z-".str_replace("_","-",$A).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$I=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($I){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $I[partrelid] AND attnum IN (".str_replace(" ",", ",$I["partattrs"]).")");$fb=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$fb[$I["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
routineTriggers($A){return
get_rows("SELECT nspname AS ns, relname AS table, tgname AS trigger
FROM pg_trigger
JOIN pg_class ON tgrelid = pg_class.oid
JOIN pg_namespace ON relnamespace = pg_namespace.oid
WHERE NOT tgisinternal AND tgfoid = substring(".q($A).", '[0-9]+\$')::oid
ORDER BY 1, 2, 3");}function
tableOid($Q){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($Q)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$H=array();$J=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($J
as$I){parse_full_type($I);$I["null"]=!$I["attnotnull"];$H[$I["tab"]][]=$I;}return$H;}function
indexAlgorithms(array$om){static$H=array();if(!$H)$H=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$H;}function
indexOpclasses(){static$H=array();if(!$H&&$this->conn->flavor!='cockroach')$H=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$H;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$hb;if($hb===null)$hb=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$hb;}function
hasEstimatedRows(){return
true;}function
isSystem($k,$K=""){return($K!=""?information_schema($k,$K)||preg_match('~^pg_~',$K):in_array($k,array("postgres","template1"))||($this->conn->flavor=='cockroach'&&$k=="system"));}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($t);}function
get_databases($pe){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($F,$Z,$y,$B=0,$ll=" "){return" $F$Z".($y?$ll."LIMIT $y".($B?" OFFSET $B":""):"");}function
limit1($Q,$F,$Z,$ll="\n"){return(preg_match('~^INTO~',$F)?limit($F,$Z,1,0,$ll):" $F".(is_view(table_status1($Q))?$Z:$ll."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$ll."LIMIT 1)"));}function
db_collation($k,array$Gb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($k));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$F="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$F
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$F
.="
ORDER BY 1";return
get_key_vals($F);}function
count_tables(array$j){$H=array();foreach($j
as$k){if(connection()->select_db($k))$H[$k]=count(tables_list());}return$H;}function
table_status($A="",$Wd=false){static$af;if($af===null)$af=get_val("SELECT 'pg_table_size'::regproc");$pl=(!$Wd&&min_version(10));$H=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($af?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	EXISTS (SELECT 1 FROM pg_index WHERE indrelid = c.oid AND NOT indisvalid)::int AS invalid_index,
	".($pl?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($pl?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($A!=""?"AND d.refobjid = ".driver()->tableOid($A):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($A!=""?"AND relname = ".q($A):"ORDER BY relname"))as$I)$H[$I["Name"]]=$I;return$H;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$I){static$xa=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$I["full_type"],$_);list(,$T,$x,$I["length"],$oa,$Ga)=$_;$I["length"].=$Ga;$tb=$T.$oa;if(isset($xa[$tb])){$I["type"]=$xa[$tb];$I["full_type"]=$I["type"].$x.$Ga;}else{$wn=idf_unescape($T);$I["type"]=(is_user_type($wn)?$wn:$T);$I["full_type"]=$I["type"].$x.$oa.$Ga;}}function
fields($Q){$H=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($Q)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$I){parse_full_type($I);$I["default_sql"]=($I["default"]!==null&&!$I["attidentity"]&&!$I["attgenerated"]&&!preg_match("~^(('(?:[^']|'')*'|NULL)(::[^,)]+)?|[-+]?[\\d.]+|true|false)$~s",$I["default"]));if(in_array($I['attidentity'],array('a','d')))$I['default']='GENERATED '.($I['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$I["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$I["attgenerated"],"");$I["composite"]=($I["typcategory"]=="C");$I["null"]=!$I["attnotnull"];$I["auto_increment"]=$I['attidentity']||preg_match('~^nextval\(~i',$I["default"])||preg_match('~^unique_rowid\(~',$I["default"]);$I["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$I['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$I["default"],$_)&&($_[2]!=""||preg_match("~^('.*'|NULL)\$~s",$_[1])))$I["default"]=($_[1]=="NULL"?null:idf_unescape($_[1]).$_[2]);$H[$I["field"]]=$I;}return$H;}function
indexes($Q,$h=null){$h=connection($h);$H=array();$vm=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $vm AND attnum > 0",$h);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indisvalid::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($h->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $vm
ORDER BY indisprimary DESC, indisunique DESC",$h)as$I){$zk=$I["relname"];$H[$zk]["type"]=($I["indisprimary"]?"PRIMARY":($I["indisunique"]?"UNIQUE":"INDEX"));$H[$zk]["columns"]=array();$H[$zk]["descs"]=array();$H[$zk]["algorithm"]=$I["amname"];$H[$zk]["partial"]=$I["partial"];$H[$zk]["invalid"]=!$I["indisvalid"];$Hf=preg_split('~(?<=\)), (?=\()~',$I["indexpr"]);foreach(explode(" ",$I["indkey"])as$If)$H[$zk]["columns"][]=($If?$e[$If]:array_shift($Hf));foreach(explode(" ",$I["indoption"])as$Jf)$H[$zk]["descs"][]=(intval($Jf)&1?'1':null);$H[$zk]["opclasses"]=($I["opclasses"]!=""?explode(" ",$I["opclasses"]):array());$H[$zk]["lengths"]=array();}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$I){$I['deferrable']=($I['deferrable']?'':'NOT ').'DEFERRABLE'.($I['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$I['definition'],$_)){$I['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$_[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$_[2],$Tg)){$I['ns']=idf_unescape($Tg[2]);$I['table']=idf_unescape($Tg[4]);}$I['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$_[3])));$I['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$_[4],$Tg)?$Tg[1]:'NO ACTION');$I['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$_[4],$Tg)?$Tg[1]:'NO ACTION');$H[$I['conname']]=$I;}}return$H;}function
view($A){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($A).")")));}function
collations(){return(isset($_GET["database"])&&DB==""&&connection()->flavor!='cockroach'&&min_version(13)?get_vals("SELECT DISTINCT collcollate FROM pg_collation WHERE collprovider = 'c' ORDER BY 1"):array());}function
information_schema($k,$K=""){$mm=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$mm[]="crdb_internal";$mm[]="pg_extension";}return
in_array($K!=""?$K:get_schema(),$mm);}function
error(){$H=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$H,$_))$H=$_[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($_[3]).'})(.*)~','\1<b>\2</b>',$_[2]).$_[4];return
nl_br($H);}function
create_database($k,$Fb){return
queries("CREATE DATABASE ".idf_escape($k).($Fb?" TEMPLATE template0 LOCALE ".q($Fb):""));}function
drop_databases(array$j){connection()->close();return
apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');}function
rename_database($A,$Fb){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($A));}function
auto_increment(){return"";}function
alter_table($Q,$A,array$o,array$re,$f,$vd,$Fb,$Na,$qj){$b=array();$hk=array();if($Q!=""&&$Q!=$A)$hk[]="ALTER TABLE ".table($Q)." RENAME TO ".table($A);$ml="";$Si=($Q!=""?fields($Q):array());foreach($o
as$n){$d=idf_escape($n[0]);$W=$n[1];if(!$W)$b[]="DROP $d";else{$Ri=$Si[$n[0]];$Pi=($Ri?process_field($Ri,$Ri):array());$Wn=$W[5];unset($W[5]);if($n[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$hk[]="ALTER TABLE ".table($A)." RENAME $d TO $W[0]";if($W[1]!=$Pi[1])$b[]="ALTER $d TYPE$W[1]";if($W[3]!=$Pi[3]||isset($W[6])!=isset($Pi[6])){$nl=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($nl).")":"DROP DEFAULT"));if(isset($W[6]))$ml="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($nl)." OWNED BY ".idf_escape($Q).".$W[0]";}if($W[2]!=$Pi[2])$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($Wn!=$Pi[5])$hk[]="COMMENT ON COLUMN ".table($A).".$W[0] IS ".($Wn!=""?substr($Wn,9):"''");}}if($Q==""){$b=array_merge($b,$re);$O="";if($qj){$Bb=(connection()->flavor=='cockroach');$O=" PARTITION BY $qj[partition_by]($qj[partition])";if($qj["partition_by"]=='HASH'){$rj=+$qj["partitions"];for($r=0;$r<$rj;$r++)$hk[]="CREATE TABLE ".idf_escape($A."_$r")." PARTITION OF ".idf_escape($A)." FOR VALUES WITH (MODULUS $rj, REMAINDER $r)";}else{$Rj="MINVALUE";foreach($qj["partition_names"]as$r=>$W){$X=$qj["partition_values"][$r];$lj=" VALUES ".($qj["partition_by"]=='LIST'?"IN ($X)":"FROM ($Rj) TO ($X)");if($Bb)$O
.=($r?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$lj";else$hk[]="CREATE TABLE ".idf_escape($A."_$W")." PARTITION OF ".idf_escape($A)." FOR$lj";$Rj=$X;}$O
.=($Bb?"\n)":"");}}array_unshift($hk,"CREATE TABLE ".table($A)." (\n".implode(",\n",$b)."\n)$O");}else{if($b)array_unshift($hk,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($re)$hk[]="ALTER TABLE ".table($A)."\n".implode(",\n",$re);}if($ml)array_unshift($hk,$ml);if($f!==null)$hk[]="COMMENT ON TABLE ".table($A)." IS ".q($f);foreach($hk
as$F){if(!queries($F))return
false;}if($Na!=""){foreach(fields($A)as$Zd=>$n){if($n["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($A)).", ".q($Zd)."), $Na)");}}return
true;}function
alter_indexes($Q,$b){$i=array();$cd=array();$hk=array();$Pb=(support("concurrent_indexes")&&$_POST["concurrently"]?" CONCURRENTLY":"");foreach($b
as$W){if($W[0]=="UNIQUE"&&$W[2]=="DROP"&&!get_val("SELECT 1 FROM pg_constraint JOIN pg_class ON conindid = pg_class.oid WHERE conrelid = ".driver()->tableOid($Q)." AND relname = ".q($W[1])))$W[0]="INDEX";if($W[0]!="INDEX")$i[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$cd[]=idf_escape($W[1]);else$hk[]="CREATE INDEX$Pb ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($i)array_unshift($hk,"ALTER TABLE ".table($Q).implode(",",$i));if($cd){$hk=array_merge(($Pb?array_map(function($t){return"DROP INDEX CONCURRENTLY $t";},$cd):array("DROP INDEX ".implode(", ",$cd))),$hk);}foreach($hk
as$F){if(!queries($F))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$H=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$A=>$R)$H[strtoupper($R["Engine"])][]=table($A);return
array_filter($H);}function
drop_views(array$do){return
drop_tables($do);}function
drop_tables(array$S){$am=array();foreach($S
as$Q)$am[$Q]=table_status1($Q);foreach(drop_kinds($am)as$qg=>$Nh){if(!queries("DROP $qg ".implode(", ",$Nh)))return
false;}return
true;}function
move_tables(array$S,array$do,$Fm){foreach(array_merge($S,$do)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($Fm)))return
false;}return
true;}function
trigger($A,$Q){if($A=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($A);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$I)$e[]=$I["event_object_column"];$H=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$I){if($e&&$I["Event"]=="UPDATE")$I["Event"].=" OF";$I["Of"]=implode(", ",$e);if($H)$I["Event"].=" OR $H[Event]";$H=$I;}return$H;}function
triggers($Q){$H=array();$Ge=array();foreach(get_rows('SELECT t.tgname, r.routine_schema AS ns, r.specific_name AS function, r.routine_name AS name
FROM pg_catalog.pg_trigger t
JOIN information_schema.routines r ON substring(r.specific_name, \'[0-9]+$\')::oid = t.tgfoid
WHERE NOT t.tgisinternal AND t.tgrelid = (
	SELECT c.oid FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = current_schema() AND c.relname = '.q($Q).'
)')as$I)$Ge[array_shift($I)]=$I;foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$I){$jn=trigger($I["trigger_name"],$Q);$H[$jn["Trigger"]]=array($jn["Timing"],$jn["Event"]);if($Ge[$jn["Trigger"]])$H[$jn["Trigger"]][]=$Ge[$jn["Trigger"]];}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($A,$T){$Hi=routine_options($T);$il=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$Hi);foreach($il
as$w=>$L)$il[$w]="$L AS \"$w\"";$J=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($il?implode(', ',$il).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($A));if(!$J)return
array();$H=$J[0];$H["definition"]=trim("$H[definition]","\r\n");$H["options"]=array_intersect_key($H,$Hi);$H["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$H[type_udt_name]"));$H["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($A)."
ORDER BY ordinal_position");return$H;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$H=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$wg)$H[$wg]=(preg_match('~sql$~',$wg)?"pgsql":"txt");return$H;}function
routine_options($Lk){$Bb=(connection()->flavor=='cockroach');$dl=($Bb?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($Lk=="PROCEDURE")return$dl;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$dl+($Bb?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($A,array$I){$H=array();foreach($I["fields"]as$n){$x=$n["length"];$H[]=$n["type"].($x?"($x)":"");}return
idf_escape($A)."(".implode(", ",$H).")";}function
last_id($G){$I=(is_object($G)?$G->fetch_row():array());return($I?$I[0]:0);}function
explain(Db$g,$F){return$g->query("EXPLAIN $F");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$yk))return$yk[1];}function
types($Sd=false){$Bb=connection()->flavor=='cockroach';$rg=($Bb?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($rg)".($Bb?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($Sd?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($s){$zd=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $s ORDER BY enumsortorder");return($zd?"'".implode("', '",array_map('addslashes',$zd))."'":"");}function
collation_name($pi){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $pi AND collname != 'default')":"NULL");}function
type_definition($s){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $s"));$H=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$H;switch($H["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $s ORDER BY enumsortorder");$H["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$I)$e[]=idf_escape($I["attname"])." $I[full_type]".($I["collation"]?" COLLATE ".idf_escape($I["collation"]):"");$H["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$Zc=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $s"));$H["definition"]="AS $Zc[base]".($Zc["collation"]?" COLLATE ".idf_escape($Zc["collation"]):"").($Zc["typdefault"]!=""?" DEFAULT $Zc[typdefault]":"").($Zc["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $s AND contype != 'n' ORDER BY conname")as$I)$H["definition"].=" CONSTRAINT ".idf_escape($I["conname"])." $I[definition]";break;case'r':$nk=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $s"));$Hi=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$w=>$Ed){if($nk[$w]!="")$Hi[]=strtoupper($w)." = ".($Ed?idf_escape($nk[$w]):$nk[$w]);}$H["definition"]="AS RANGE (".implode(", ",$Hi).")";}return$H;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($K,$h=null){$_GET["ns"]=$K;$H=get_val("SELECT set_config('search_path', ".q(idf_escape($K)).", false) FROM pg_namespace WHERE nspname = ".q($K),0,$h);driver()->setUserTypes(types(true));return!!$H;}function
schema_comment($K){return(string)get_val("SELECT obj_description(oid, 'pg_namespace') FROM pg_namespace WHERE nspname = ".q($K));}function
set_schema_comment($K,$f){return!!queries("COMMENT ON SCHEMA ".idf_escape($K)." IS ".q($f));}function
drop_sql(array$S){$H="";foreach(drop_kinds($S)as$qg=>$Nh)$H
.="DROP $qg IF EXISTS ".implode(", ",$Nh).";\n";return($H?"$H\n":"");}function
foreign_keys_sql($Q){$H="";$me=foreign_keys($Q);ksort($me);foreach($me
as$le=>$ke){$H
.="ALTER TABLE ONLY ".table($Q)." ADD CONSTRAINT ".idf_escape($le)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$_){return$_[1].table(idf_unescape($_[2]))."(";},$ke["definition"]).";\n";}return($H?"$H\n":$H);}function
indexes_sql($Q,$Uj=""){$H="";$F="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($Q).($Uj!=""?" AND indexname != ".q($Uj):"");foreach(get_rows($F,null,"-- ")as$I)$H
.="\n\n".str_replace(array(" $I[db].$I[qualified] USING "," $I[qualified] USING ")," ".table($Q)." USING ",$I["indexdef"]).";";return$H;}function
create_sql($Q,$Na,$em){$Gk=array();$pl=array();$ql=array();$ol=array();$O=table_status1($Q);if(is_view($O)){$co=view($Q);$i="CREATE ".strtoupper($O["Engine"])." ".table($Q)." AS ".rtrim($co["select"],";").";";return
rtrim($i.indexes_sql($Q),';');}$o=fields($Q);if(count($O)<2||empty($o))return"";$H="CREATE TABLE ".table($O['Name'])." (\n    ";$tm=q(table($O['Name']));foreach($o
as$n){$rl="";if($n['default']=="nextval('$O[Name]_$n[field]_seq')"){$rl=table("$O[Name]_$n[field]_seq");$n['default']=null;$n['full_type']=preg_replace('~int(eger)?~','serial',$n['full_type']);}$jj=idf_escape($n['field']).' '.full_type_sql($n).preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$_){return$_[1].str_replace("'","''",table(idf_unescape($_[2])))."'";},default_value($n)).($n['null']?"":" NOT NULL");$Gk[]=$jj;if(preg_match('~nextval\(\'([^\']+)\'\)~',$n['default'],$Ug)){$nl=$Ug[1];$Ol=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($nl)):"SELECT * FROM $nl"),null,"-- "));$ml=table(idf_unescape($nl));$pl[]=($em=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $ml;\n":"")."CREATE SEQUENCE $ml INCREMENT $Ol[increment_by] MINVALUE $Ol[min_value] MAXVALUE $Ol[max_value]"." CACHE $Ol[cache_value];";if(get_val("SELECT pg_get_serial_sequence($tm, ".q($n['field']).")"))$ql[]="\n\nALTER SEQUENCE $ml OWNED BY ".table($O['Name']).".".idf_escape($n['field']).";";if($Na)$ol[]=$ml;}elseif($Na&&$n['auto_increment']){$ml=($rl?"":get_val("SELECT pg_get_serial_sequence($tm, ".q($n['field']).")::regclass"));$ol[]=($ml?table(idf_unescape($ml)):$rl);}}if(!empty($pl))$H=implode("\n\n",$pl)."\n\n$H";$Uj="";foreach(indexes($Q)as$Ff=>$u){if($u['type']=='PRIMARY'){$Uj=$Ff;$Gk[]="CONSTRAINT ".idf_escape($Ff)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$u['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$Tb=>$Vb)$Gk[]="CONSTRAINT ".idf_escape($Tb)." CHECK ($Vb)";$H
.=implode(",\n    ",$Gk)."\n)";$lj=driver()->partitionsInfo($O['Name']);if($lj)$H
.="\nPARTITION BY $lj[partition_by]($lj[partition])";$H
.=(min_version(12)?"":"\nWITH (oids = ".($O['Oid']?'true':'false').")").";";$H
.=implode($ql);if($O['Comment'])$H
.="\n\nCOMMENT ON TABLE ".table($O['Name'])." IS ".q($O['Comment']).";";foreach($o
as$Zd=>$n){if($n['comment'])$H
.="\n\nCOMMENT ON COLUMN ".table($O['Name']).".".idf_escape($Zd)." IS ".q($n['comment']).";";}$H
.=indexes_sql($Q,$Uj);foreach(array_filter($ol)as$ml){$Ol=first(get_rows("SELECT last_value, is_called::int FROM $ml",null,"-- "));if($Ol['is_called'])$H
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($ml).", $Ol[last_value]); END \$\$;";}return
rtrim($H,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$H="";foreach(triggers($Q)as$in=>$hn){$jn=trigger($in,$O['Name']);$H
.="\nCREATE TRIGGER ".idf_escape($jn['Trigger'])." $jn[Timing] $jn[Event] ON ".table($O['Name'])." $jn[Type] $jn[Statement];;\n";}return$H;}function
use_sql($uc,$em=""){$A=idf_escape($uc);$H="";if(preg_match('~CREATE~',$em)){if($em=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $A;\n";$H
.="CREATE DATABASE $A;\n";}return"$H\\connect $A";}function
use_schema_sql($K,$em){$A=idf_escape($K);$H="";if(preg_match('~CREATE~',$em)){if($em=="DROP+CREATE")$H="DROP SCHEMA IF EXISTS $A CASCADE;\n";$H
.="CREATE SCHEMA IF NOT EXISTS $A;\n";$f=schema_comment($K);if($f!="")$H
.="COMMENT ON SCHEMA $A IS ".q($f).";\n";}return$H."SET search_path TO $A";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$n){if(preg_match('~^(geometry|geography)$~',$n["type"])&&strpos($n["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($n["field"]).")";}function
unconvert_field(array$n,$H){return($n["composite"]?"$H::".full_type_sql($n):$H);}function
support($Xd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|transaction_ddl').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'||!min_version(9.2)?'':'|concurrent_indexes').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$Xd);}function
kill_process($s){return
queries("SELECT pg_terminate_backend(".number($s).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$M,$U,$D){$this->link=new
\SQLite3($M["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer(array($this,'authorize'));$bo=\SQLite3::version();$this->server_info=$bo["versionString"];return'';}function
query($F,$vn=false){$G=@$this->link->query($F);$this->error="";if(!$G){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($G->numColumns())return
new
Result($G);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$un=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$un[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$M,$U,$D){$H=$this->dsn(DRIVER.":".$M["path"],"","",array(),(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$H&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer(array($this,'authorize'));return$H;}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}function
store_result($G=null){$ua=$this->affected_rows;$H=parent::store_result($G);if(!$G&&$H===true)$this->affected_rows=$ua;return$H;}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;private$total_changes=0;function
attach(array$M,$U,$D){parent::attach($M,$U,$D);$this->total_changes=0;$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
query($F,$vn=false){$H=parent::query($F,$vn);if($this->affected_rows){$G=parent::query("SELECT total_changes()");$I=($G?$G->fetch_row():false);if($I){if($I[0]==$this->total_changes)$this->affected_rows=0;$this->total_changes=$I[0];}}return$H;}function
select_db($p){$F="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$p)?$p:dirname($_SERVER["SCRIPT_FILENAME"])."/$p")." AS a";$this->attaching=true;$Ka=is_readable($p)&&$this->query($F);$this->attaching=false;if($Ka)return!self::attach(server_parts(array("path"=>$p)),'','');return
false;}function
authorize($ka,$Ea){return($ka!=24||$Ea===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array("date"=>"current_date","time"=>"current_timestamp",);var$editFunctions=array("^(?!.*(char|clob|text|blob))."=>"+/-","char|clob|text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($om){$H=array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($om,"Engine")))$H[]="MATCH";$H[]="SQL";return$H;}static
function
connect($M,$U,$D){return
parent::connect(":memory:","","");}function
__construct(Db$g){parent::__construct($g);if(min_version(3.31,0,$g))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$g))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Uk){return"x".q(bin2hex($Uk));}function
typeName(\stdClass$n){$H=strtolower(idx((array)$n,'sqlite:decl_type',parent::typeName($n)));return
idx(array("string"=>"text","double"=>"real"),$H,$H);}function
engines(){$H=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$H[]="STRICT";$H[]="STRICT, WITHOUT ROWID";}$H[]="WITHOUT ROWID";}return$H;}private
function
isVirtual(array$R){$vd=$R["Engine"];return$vd!=""&&!in_array($vd,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$om){return!$this->isVirtual($om);}function
shadowTables($Q){$H=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$A){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$A))$H[]=array("table"=>$A,"ns"=>"");}}return$H;}function
fulltextSql($A,array$u,$F,$cb){return
idf_escape($A)." MATCH ".q($F);}function
insertUpdate($Q,array$J,array$Uj){$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($J))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($A,$fg=false){if(preg_match('~^sqlite_(seq|stat.)~',$A,$_))return"fileformat2.html#$_[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$A))return"schematab.html";}function
checkConstraints($Q){$i=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn);preg_match_all('~\s(?:CONSTRAINT\s+("(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|\S+)\s+)?CHECK\s*(\(((?>[^()]+)|(?2))*\))~',$i,$Ug,PREG_SET_ORDER);$H=array();foreach($Ug
as$_){$Ab=trim(substr($_[2],1,-1));$H[$_[1]!=""?idf_unescape($_[1]):$Ab]=$Ab;}return$H;}function
allFields(){$H=array();if(min_version(3.16)){$J=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($J
as$I){$I["type"]=type_affinity($I["type"]);$I["null"]=!$I["notnull"];$H[$I["tab"]][]=$I;}}else$H=parent::allFields();return$H;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($pe){return
array();}function
limit($F,$Z,$y,$B=0,$ll=" "){return" $F$Z".($y?$ll."LIMIT $y".($B?" OFFSET $B":""):"");}function
limit1($Q,$F,$Z,$ll="\n"){return(preg_match('~^INTO~',$F)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($F,$Z,1,0,$ll):" $F WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$ll."LIMIT 1)");}function
db_collation($k,array$Gb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Pl){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Pl,$_)?$_[1]:"");}function
parse_comments($Pl){$H=array();if(virtual_module($Pl)!="")return$H;preg_match_all('~\'(?:[^\']|\'\')*\'|"(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|--[^\n]*|/\*.*?\*/|[(),\n]|[^-/\'"`[(),\n]+|[-/]~s',$Pl,$Ug);$Hc=0;$Nh=array();$xg="";$ai=true;foreach($Ug[0]as$Xm){if(preg_match('~^--~',$Xm)){if($Hc==1&&!$ai){$A=($xg=="("?"":($xg==","?idx($Nh,count($Nh)-2):end($Nh)));if(is_string($A))$H[$A]=trim(substr($Xm,2));}}elseif($Xm=="\n")$ai=true;elseif(trim($Xm)!=""&&!preg_match('~^/\*~',$Xm)){$ai=false;$xg=($Hc==1&&$Xm==","?",":($Hc==0&&$Xm=="("?"(":""));if($xg)$Nh[]=false;elseif($Hc==1&&end($Nh)===false&&$Xm!="("&&$Xm!=")"){$lk=preg_match('~^[\'"`[]~',$Xm);$po=($lk?idf_unescape($Xm):preg_replace('~\s.*~s','',trim($Xm)));$Nh[count($Nh)-1]=($lk||!preg_match('~^(CONSTRAINT|PRIMARY|UNIQUE|CHECK|FOREIGN)$~i',$po)?$po:null);}$Hc+=($Xm=="("?1:($Xm==")"?-1:0));}}return$H;}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$j){return
array();}function
db_status(){$cj=get_val("PRAGMA page_size");$ze=get_val("PRAGMA freelist_count")*$cj;return
array("Data_length"=>get_val("PRAGMA page_count")*$cj-$ze,"Index_length"=>0,"Data_free"=>$ze,);}function
table_status($A="",$Wd=false){$H=array();$J=array();if(!$Wd&&$A==""){connection()->query("PRAGMA optimize = 0x10002");$J=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($A!=""?"AND name = ".q($A):"ORDER BY (name LIKE 'sqlite_%'), name"))as$I){if($I["Engine"]=="table"){$Pl=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$I["sql"]);$gm=preg_replace('~.*\)~s','',$Pl);$I["Engine"]=virtual_module($I["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$gm)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$gm)?"WITHOUT ROWID":0),)))?:"table");$I["Comment"]=idx(parse_comments($I["sql"]),"","");}unset($I["sql"]);$I["Rows"]=idx($J,$I["Name"],0);$H[$I["Name"]]=$I;}if(!$Wd){foreach(get_rows("SELECT * FROM sqlite_sequence".($A!=""?" WHERE name = ".q($A):""),null,"")as$I)$H[$I["name"]]["Auto_increment"]=$I["seq"];}return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$H=array();$Pl=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$Zj=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$Zj+=array("insert"=>1,"update"=>1);$Ae=preg_match('~^fts\d+$~i',virtual_module($Pl));$Nb=parse_comments($Pl);foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$I){if($I["hidden"]==1)continue;$A=$I["name"];$T=strtolower($I["type"]);$l=$I["dflt_value"];preg_match('~^(.+?)\s*(?:\((.*)\))?$~s',$T,$tj);$lk=preg_match("~^'(?:[^']|'')*'$~",$l);$H[$A]=array("field"=>$A,"type"=>($Ae?"text":($tj?$tj[1]:type_affinity($T))),"full_type"=>$T,"length"=>($Ae?"":idx($tj,2,"")),"default"=>($lk?idf_unescape($l):($l=="NULL"?null:$l)),"default_sql"=>($l!==null&&!$lk&&$l!="NULL"&&!is_numeric($l)),"null"=>!$I["notnull"],"privileges"=>$Zj,"primary"=>$I["pk"],"comment"=>idx($Nb,$A,""),);if($I["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Pl))$H[$A]["auto_increment"]=true;}$t='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$Ck='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$t.'\s+text\b'.$Ck.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Pl,$Ug,PREG_SET_ORDER);foreach($Ug
as$_){$A=idf_unescape($_[1]);if($H[$A])$H[$A]["collation"]=trim($_[3],"'");}preg_match_all('~'.$t.'\s'.$Ck.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Pl,$Ug,PREG_SET_ORDER);foreach($Ug
as$_){$A=idf_unescape($_[1]);if($H[$A]){$H[$A]["default"]=$_[3];$H[$A]["generated"]=strtoupper($_[4]);}}return$H;}function
indexes($Q,$h=null){$h=connection($h);$H=array();$Pl=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$h);if(preg_match('~^fts\d+$~i',virtual_module($Pl)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Pl,$_)){$H[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$_[1],$Ug,PREG_SET_ORDER);foreach($Ug
as$_){$H[""]["columns"][]=idf_unescape($_[2]).$_[4];$H[""]["descs"][]=(preg_match('~DESC~i',$_[5])?'1':null);}}if(!$H){foreach(fields($Q)as$A=>$n){if($n["primary"])$H[""]=array("type"=>"PRIMARY","columns"=>array($A),"lengths"=>array(),"descs"=>array(null));}}$Ul=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$h);foreach(get_rows("PRAGMA index_list(".table($Q).")",$h)as$I){$A=$I["name"];$u=array("type"=>($I["unique"]?"UNIQUE":"INDEX"));$u["lengths"]=array();$u["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($A).")",$h)as$Sk){$u["columns"][]=$Sk["name"];$u["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($A).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$Ul[$A],$yk)){preg_match_all('/("[^"]*+")+( DESC)?/',$yk[2],$Ug);foreach($Ug[2]as$w=>$W){if($W)$u["descs"][$w]='1';}}if(!$H[""]||$u["type"]!="UNIQUE"||$u["columns"]!=$H[""]["columns"]||$u["descs"]!=$H[""]["descs"]||!preg_match("~^sqlite_~",$A))$H[$A]=$u;}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$I){$q=&$H[$I["id"]];if(!$q)$q=$I;$q["source"][]=$I["from"];$q["target"][]=$I["to"];}return$H;}function
view($A){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+(?=\S)~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($A))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($k,$K=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($A){$Sd="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Sd)\$~",$A)){connection()->error=lang(36,str_replace("|",", ",$Sd));return
false;}return
true;}function
create_database($k,$Fb){if(file_exists($k)){connection()->error=lang(37);return
false;}if(!check_sqlite_name($k))return
false;try{$z=new
Db();$z->attach(server_parts(array("path"=>$k)),'','');}catch(\Exception$Id){connection()->error=$Id->getMessage();return
false;}$z->query('PRAGMA encoding = "UTF-8"');$z->query('CREATE TABLE adminer (i)');$z->query('DROP TABLE adminer');return
true;}function
drop_databases(array$j){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($j
as$k){if(!check_sqlite_name($k))return
false;if(!@unlink($k)){connection()->error=lang(37);return
false;}}return
true;}function
rename_database($A,$Fb){if(!check_sqlite_name($A))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(37);return@rename(DB,$A);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$A,array$o,array$re,$f,$vd,$Fb,$Na,$qj){$Mn=($Q==""||$re||$vd||$f!==null);foreach($o
as$n){if($n[0]!=""||!$n[1]||$n[2]||$n[1][5]){$Mn=true;break;}}$b=array();$Wi=array();foreach($o
as$n){if($n[1]){$b[]=($Mn?$n[1]:"ADD ".implode($n[1]));if($n[0]!="")$Wi[$n[0]]=$n[1][0];}}if(!$Mn){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$A&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($A)))return
false;}elseif(!recreate_table($Q,$A,$b,$Wi,$re,$Na,array(),"","",$vd,$f))return
false;if($Na){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Na WHERE name = ".q($A));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($A).", $Na)");queries("COMMIT");}return
true;}function
recreate_table($Q,$A,array$o,array$Wi,array$re,$Na="",$v=array(),$dd="",$na="",$vd="",$f=null){if($Q!=""){if(!$o){foreach(fields($Q)as$w=>$n){if($v)$n["auto_increment"]=0;$o[]=process_field($n,$n);$Wi[$w]=idf_escape($w);}}$Vj=false;foreach($o
as$n){if($n[6])$Vj=true;}$fd=array();foreach($v
as$w=>$W){if($W[2]=="DROP"){$fd[$W[1]]=true;unset($v[$w]);}}foreach(indexes($Q)as$mg=>$u){$e=array();foreach($u["columns"]as$w=>$d){if(!$Wi[$d])continue
2;$e[]=$Wi[$d].($u["descs"][$w]?" DESC":"");}if(!$fd[$mg]){if($u["type"]!="PRIMARY"||!$Vj)$v[]=array($u["type"],$mg,$e);}}foreach($v
as$w=>$W){if($W[0]=="PRIMARY"){unset($v[$w]);$re[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}elseif(preg_match('~^sqlite_autoindex_~',$W[1])){unset($v[$w]);$re[]="  UNIQUE (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$mg=>$q){foreach($q["source"]as$w=>$d){if(!$Wi[$d])continue
2;$q["source"][$w]=idf_unescape($Wi[$d]);}if(!isset($re[" $mg"]))$re[]=" ".format_foreign_key($q);}queries("PRAGMA foreign_keys = 0");queries("PRAGMA legacy_alter_table = 1");queries("BEGIN");}$nb=array();$Nb=array();foreach($o
as$n){if(preg_match('~GENERATED~',$n[3]))unset($Wi[array_search($n[0],$Wi)]);if(preg_match("~^ COMMENT ('.*')$~s",$n[5],$_))$Nb[count($nb)]=idf_unescape($_[1]);unset($n[5]);$nb[]="  ".implode($n);}$nb=array_merge($nb,array_values(array_filter($re)));foreach(driver()->checkConstraints($Q)as$w=>$rb){if($w!=$dd)$nb[]=" ".($w!=$rb?" CONSTRAINT ".idf_escape($w):"")." CHECK ($rb)";}if($na)$nb[]="  $na";$Jm=($Q!=""&&$Q==$A?"adminer_$A":$A);if($Q!=""&&(!$vd||$f===null)){$O=table_status1($Q);$vd=$vd?:idx($O,"Engine");$f=($f===null?idx($O,"Comment",""):$f);}$Jg=function($Lm){return($Lm!=""?" -- ".preg_replace('~[\r\n]+~',' ',$Lm):"");};$i="CREATE TABLE ".table($Jm)." (".$Jg($f)."\n";foreach($nb
as$r=>$mb)$i
.=$mb.($r<count($nb)-1?",":"").$Jg(idx($Nb,$r))."\n";if(!queries("$i)".($vd!="table"&&in_array($vd,driver()->engines())?" $vd":"")))return
false;if($Q!=""){if($Wi&&!queries("INSERT INTO ".table($Jm)." (".implode(", ",$Wi).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Wi)))." FROM ".table($Q)))return
false;$nn=array();foreach(triggers($Q)as$ln=>$Qm){$jn=trigger($ln,$Q);$nn[]="CREATE TRIGGER ".idf_escape($ln)." ".implode(" ",$Qm)." ON ".table($A)."\n$jn[Statement]";}$Na=$Na?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$A&&!queries("ALTER TABLE ".table($Jm)." RENAME TO ".table($A)))||!alter_indexes($A,$v))return
false;if($Na)queries("UPDATE sqlite_sequence SET seq = $Na WHERE name = ".q($A));foreach($nn
as$jn){if(!queries($jn))return
false;}if(get_rows("PRAGMA foreign_key_check(".table($A).")")){connection()->error="FOREIGN KEY constraint failed";return
false;}queries("COMMIT");queries("PRAGMA foreign_keys = 1");queries("PRAGMA legacy_alter_table = 0");}return
true;}function
index_sql($Q,$T,$A,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($A!=""?$A:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$Uj){if($Uj[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$do){return
apply_queries("DROP VIEW",$do);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$do,$Fm){return
false;}function
trigger($A,$Q){if($A=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$t='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$mn=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$t\\s*(".implode("|",$mn["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($t))?\\s+ON\\s*$t\\s*(?:FOR\\s+EACH\\s+ROW\\s+)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($A)),$_);if(!$_)return
array();$ki=$_[3];return
array("Timing"=>strtoupper($_[1]),"Event"=>strtoupper($_[2]).($ki?" OF":""),"Of"=>idf_unescape($ki),"Trigger"=>$A,"Statement"=>$_[4],);}function
triggers($Q){$H=array();$mn=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$I){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$mn["Timing"]).')\s*(.*?)\s+ON\b~i',$I["sql"],$_);$H[$I["name"]]=array($_[1],$_[2]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$g,$F){return$g->query("EXPLAIN QUERY PLAN $F");}function
found_rows(array$R,array$Z){}function
types($Sd=false){return
array();}function
create_sql($Q,$Na,$em){$H=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$A=>$u){if($A==''||$u['type']=='FULLTEXT')continue;$H
.=";\n\n".index_sql($Q,$u['type'],$A,"(".implode(", ",array_map('Adminer\idf_escape',$u['columns'])).")");}return$H;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($uc,$em=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$H=array();foreach(get_rows("PRAGMA pragma_list")as$I){$A=$I["name"];if($A!="pragma_list"&&$A!="compile_options"){$H[$A]=array($A,'');foreach(get_rows("PRAGMA $A")as$I)$H[$A][1].=implode(", ",$I)."\n";}}return$H;}function
show_status(){$H=array();foreach(get_vals("PRAGMA compile_options")as$Fi)$H[]=explode("=",$Fi,2)+array('','');return$H;}function
convert_field(array$n){}function
unconvert_field(array$n,$H){return$H;}function
support($Xd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$Xd);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="SQLSRV";private$link,$result,$warnings,$transaction=false;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$m){$this->errno=$m["code"];$this->error
.="$m[message]\n";}$this->error=rtrim($this->error);}function
attach(array$M,$U,$D){sqlsrv_configure("WarningsReturnAsErrors",0);$Ub=array("UID"=>$U,"PWD"=>$D,"CharacterSet"=>"UTF-8","ReturnDatesAsStrings"=>true);if(isset($_GET["sql"])&&!self::$instance)$Ub["MultipleActiveResultSets"]=false;$Vl=adminer()->connectSsl();if(isset($Vl["Encrypt"]))$Ub["Encrypt"]=$Vl["Encrypt"];if(isset($Vl["TrustServerCertificate"]))$Ub["TrustServerCertificate"]=$Vl["TrustServerCertificate"];$k=adminer()->database();if($k!="")$Ub["Database"]=$k;$Hj=$M["port"];$this->link=@sqlsrv_connect($M["host"].($Hj?",$Hj":""),$Ub);if($this->link){$Kf=sqlsrv_server_info($this->link);$this->server_info=$Kf['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){return
unicode_prefix($P)."'".str_replace("'","''",$P)."'";}function
select_db($uc){return$this->query(use_sql($uc));}function
query($F,$vn=false){$G=sqlsrv_query($this->link,$F);$this->error="";if(!$G){$this->get_error();return
false;}return$this->store_result($G);}function
multi_query($F){$this->result=sqlsrv_query($this->link,$F);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($G=null){if(!$G)$G=$this->result;if(!$G)return
false;$this->warnings=sqlsrv_errors(SQLSRV_ERR_WARNINGS);if(sqlsrv_field_metadata($G))return
new
Result($G);$this->affected_rows=sqlsrv_rows_affected($G);return
true;}function
next_result(){if(!$this->result)return
false;$H=sqlsrv_next_result($this->result);if($H===false){$this->get_error();$this->result=null;return
true;}return!!$H;}function
warnings(){$H=array();foreach((array)$this->warnings
as$go)$H[]=$go["message"];return$H;}function
inTransaction(){return$this->transaction||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
begin(){$this->transaction=sqlsrv_begin_transaction($this->link);if(!$this->transaction)$this->get_error();return$this->transaction;}function
commit(){if($this->transaction&&!sqlsrv_commit($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}function
rollback(){if(!$this->transaction&&isset($_GET["sql"]))return!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK");if($this->transaction&&!sqlsrv_rollback($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC);}function
fetch_row(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC);}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$un=array(-155=>"datetimeoffset","time",-152=>"xml","varbinary","sql_variant",-11=>"uniqueidentifier","ntext","nvarchar","nchar","bit","tinyint","bigint","image","varbinary","binary","text",1=>"char","numeric","decimal","int","smallint","float","real","float",12=>"varchar",91=>"date","time","datetime",);$n=$this->fields[$this->offset++];$H=new
\stdClass;$H->name=$n["Name"];$H->native_type=idx($un,$n["Type"],"");return$H;}function
seek($B){for($r=0;$r<$B;$r++)sqlsrv_fetch($this->result);}}function
last_id($G){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$g,$F){$g->query("SET SHOWPLAN_ALL ON");$H=$g->query($F);$g->query("SET SHOWPLAN_ALL OFF");return$H;}}else{abstract
class
MssqlDb
extends
PdoDb{function
quote($P){return
unicode_prefix($P).parent::quote($P);}function
select_db($uc){return$this->query(use_sql($uc));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
inTransaction(){return
parent::inTransaction()||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT"));}function
rollback(){return(parent::inTransaction()||!isset($_GET["sql"])?parent::rollback():!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK"));}function
warnings(){$G=$this->multi;if(!is_object($G))return
array();$m=$G->errorInfo();return
array((string)$m[2]);}}function
last_id($G){return
connection()->lastInsertId();}function
explain(Db$g,$F){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach(array$M,$U,$D){$Hj=$M["port"];$hd="sqlsrv:Server=$M[host]".($Hj?",$Hj":"").(isset($_GET["sql"])&&!self::$instance?";MultipleActiveResultSets=0":"");$Vl=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$w){if(isset($Vl[$w]))$hd
.=";$w=".($Vl[$w]?1:0);}return$this->dsn($hd,$U,$D,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$M,$U,$D){$Hj=$M["port"];$Hl=$M["socket"];$Hi=array(1002=>true);$H=$this->dsn("dblib:charset=utf8;host=$M[host]".($Hj!=""?";port=$Hj":($Hl!=""?";unix_socket=$Hl":"")),$U,$D,$Hi);if(!$H){$this->query("SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");$this->server_info=get_val("SELECT CAST(SERVERPROPERTY('ProductVersion') AS varchar(20))",0,$this);}return$H;}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="|OUTPUT";private$unknownTypes=array();function
operators($om){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($M,$U,$D){if($M=="")$M="localhost:1433";return
parent::connect($M,$U,$D);}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),lang(30)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>23,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),lang(31)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),lang(32)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),lang(34)=>array("geometry"=>0,"geography"=>0),);$un=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($un){foreach($this->types
as$Me=>$Pe){foreach($Pe
as$T=>$x){if(isset($un[$T]))unset($un[$T]);else
unset($this->types[$Me][$T]);}if(!$this->types[$Me])unset($this->types[$Me]);}$this->unknownTypes=array_keys($un);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
typeName(\stdClass$n){return
idx((array)$n,'sqlsrv:decl_type',parent::typeName($n));}function
insertUpdate($Q,array$J,array$Uj){$o=fields($Q);$Fn=array();$Z=array();$N=reset($J);$e="c".implode(", c",range(1,count($N)));$gb=0;$Qf=array();foreach($N
as$w=>$W){$gb++;$A=idf_unescape($w);if(!$o[$A]["auto_increment"])$Qf[$w]="c$gb";if(isset($Uj[$A]))$Z[]="$w = c$gb";else$Fn[]="$w = c$gb";}$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";if($Z){$tf=queries("SET IDENTITY_INSERT ".table($Q)." ON");$H=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($Fn?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Fn):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($tf?$N:$Qf)).") VALUES (".($tf?$e:implode(", ",$Qf)).");");if($tf)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$H=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($N)).") VALUES\n".implode(",\n",$Y));return$H;}function
begin(){remember_query("BEGIN TRANSACTION");return$this->conn->begin();}function
convertSearch($t,array$W,array$n){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$n["type"])?"CAST($t AS nvarchar(max))":$t);}function
quoteBinary($Uk){return"0x".bin2hex($Uk);}function
warnings(){$H=array();foreach($this->conn->warnings()as$mh){$mh=trim(preg_replace('~^(\[[^]]+])+~','',$mh));if($mh!="")$H[]=$mh;}return
nl_br(h(implode("\n",$H)));}function
tableHelp($A,$fg=false){$Kg=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$z=$Kg[get_schema()];if($z)return"relational-databases/system-$z".preg_replace('~_~','-',strtolower($A))."-transact-sql";}function
isSystem($k,$K=""){return($K!=""?information_schema($k,$K)||preg_match('~^(guest|db_(owner|accessadmin|securityadmin|ddladmin|backupoperator|(deny)?data(reader|writer)))$~',$K):in_array($k,array("master","tempdb","model","msdb")));}}function
unicode_prefix($P){return(strlen($P)!=utf8_length($P)?"N":"");}function
idf_escape($t){return"[".str_replace("]","]]",$t)."]";}function
table($t){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($t);}function
get_databases($pe){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($F,$Z,$y,$B=0,$ll=" "){return($y?" TOP (".($y+$B).")":"")." $F$Z";}function
limit1($Q,$F,$Z,$ll="\n"){return
limit($F,$Z,1,0,$ll);}function
db_collation($k,array$Gb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($k));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$j){$H=array();foreach($j
as$k){connection()->select_db($k);$H[$k]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$H;}function
table_status($A="",$Wd=false){$H=array();$Fl=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$I){$ji=$I["object_id"];unset($I["object_id"]);$Fl[$ji]=$I;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment,
	CASE WHEN EXISTS (SELECT 1 FROM sys.indexes i WHERE i.object_id = ao.object_id AND i.is_disabled = 1) THEN 1 END AS invalid_index
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($A!=""?"AND name = ".q($A):"ORDER BY name"))as$I){$ji=$I["object_id"];unset($I["object_id"]);$H[$I["Name"]]=$I+idx($Fl,$ji,array());}return$H;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
type_length($T,array$I){return(preg_match("~char|binary~",$T)?($I["max_length"]==-1?"max":intval($I["max_length"])/($T[0]=='n'?2:1)):($T=="decimal"?"$I[precision],$I[scale]":(preg_match('~^(datetime2|datetimeoffset|time)$~',$T)?$I["scale"]:($T=="vector"?(intval($I["max_length"])-8)/4:""))));}function
fields($Q){$Nb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$H=array();$pm=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($pm))as$I){$T=$I["type"];$x=type_length($T,$I);$l=$I["default"];$Bc=false;if(preg_match("~^\\(N?('(?:[^']|'')*')\\)$~s",$l,$_))$l=idf_unescape($_[1]);elseif(preg_match('~^\(\(([-+]?[\d.]+)\)\)$~',$l,$_))$l=$_[1];elseif($l!==null){$l=preg_replace('~^\((.*)\)$~s','\1',$l);$Bc=true;}$H[$I["name"]]=array("field"=>$I["name"],"full_type"=>$T.($x!=""?"($x)":""),"type"=>$T,"length"=>$x,"default"=>$l,"default_sql"=>$Bc,"default_constraint"=>$I["default_constraint"],"null"=>$I["is_nullable"],"auto_increment"=>$I["is_identity"],"collation"=>$I["collation_name"],"privileges"=>($T=="timestamp"?array("select"=>1,"where"=>1,"order"=>1):array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1)),"primary"=>$I["is_primary_key"],"comment"=>$Nb[$I["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($pm))as$I){$H[$I["name"]]["generated"]=($I["is_persisted"]?"PERSISTED":"VIRTUAL");$H[$I["name"]]["default"]=$I["definition"];}return$H;}function
indexes($Q,$h=null){$H=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, is_disabled, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$h)as$I){$A=$I["name"];$H[$A]["type"]=($I["is_primary_key"]?"PRIMARY":($I["is_unique"]?"UNIQUE":"INDEX"));$H[$A]["lengths"]=array();$H[$A]["invalid"]=!!$I["is_disabled"];$H[$A]["columns"][$I["key_ordinal"]]=$I["column_name"];$H[$A]["descs"][$I["key_ordinal"]]=($I["is_descending_key"]?'1':null);}return$H;}function
view($A){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($A))));}function
collations(){$H=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$Fb)$H[preg_replace('~_.*~','',$Fb)][]=$Fb;return$H;}function
information_schema($k,$K=""){return
in_array($K!=""?$K:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($k,$Fb){return
queries("CREATE DATABASE ".idf_escape($k).(preg_match('~^[a-z0-9_]+$~i',$Fb)?" COLLATE $Fb":""));}function
drop_databases(array$j){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$j)));}function
rename_database($A,$Fb){if(preg_match('~^[a-z0-9_]+$~i',$Fb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $Fb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($A));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$A,array$o,array$re,$f,$vd,$Fb,$Na,$qj){$b=array();$Nb=array();$Si=fields($Q);foreach($o
as$n){$d=idf_escape($n[0]);$W=$n[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$Qi=idx($Si[$n[0]],"comment");if($W[5]!=($Qi!=""?" COMMENT ".q($Qi):""))$Nb[idf_unescape($W[0])]=array($Qi!="",substr($W[5],9));unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($n[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($re[$W[0]],16+strlen($W[0])):"");else{$l=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$Ri=$Si[$n[0]];if(default_value($Ri)!=$l){if($Ri["default"]!==null)$b["DROP"][]=" ".idf_escape($Ri["default_constraint"]);if($l)$b["ADD"][]="\n $l FOR $d";}}}}if($Q==""){$ma=(array)$b["ADD"];foreach($re
as$w=>$W){if(!is_string($w))$ma[]="\n$W";}if(!queries("CREATE TABLE ".table($A)." (".implode(",",$ma)."\n)"))return
false;}else{if($Q!=$A)queries("EXEC sp_rename ".q(table($Q)).", ".q($A));if($re)$b[""]=$re;foreach($b
as$w=>$W){if(!queries("ALTER TABLE ".table($A)." $w".implode(",",$W)))return
false;}}$dk="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($A);foreach($Nb
as$w=>$W){list($cd,$Lb)=$W;$ek="$dk, @level2type = N'Column', @level2name = ".q($w);if($cd)queries("EXEC sp_dropextendedproperty $ek");if($Lb!="")queries("EXEC sp_addextendedproperty $ek, @value = $Lb");}if($f!==null){if(idx(table_status1($A),"Comment")!="")queries("EXEC sp_dropextendedproperty $dk");if($f!="")queries("EXEC sp_addextendedproperty $dk, @value = ".q($f));}return
true;}function
alter_indexes($Q,$b){$u=array();$cd=array();$hk=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY"||($W[0]=="UNIQUE"&&get_val("SELECT is_unique_constraint FROM sys.indexes WHERE object_id = OBJECT_ID(".q(table($Q)).") AND name = ".q($W[1]))))$cd[]=idf_escape($W[1]);else$u[]=idf_escape($W[1])." ON ".table($Q);}else$hk[]=($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")";}if($cd)array_unshift($hk,"ALTER TABLE ".table($Q)." DROP ".implode(", ",$cd));if($u)array_unshift($hk,"DROP INDEX ".implode(", ",$u));foreach($hk
as$F){if(!queries($F))return
false;}return
true;}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$H=array();$zi=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$K=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($K))as$I){$q=&$H[$I["FK_NAME"]];$q["db"]=($I["PKTABLE_QUALIFIER"]==DB?"":$I["PKTABLE_QUALIFIER"]);$q["ns"]=($I["PKTABLE_OWNER"]==$K?"":$I["PKTABLE_OWNER"]);$q["table"]=$I["PKTABLE_NAME"];$q["on_update"]=$zi[$I["UPDATE_RULE"]];$q["on_delete"]=$zi[$I["DELETE_RULE"]];$q["source"][]=$I["FKCOLUMN_NAME"];$q["target"][]=$I["PKCOLUMN_NAME"];}return$H;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$do){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$do)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$do,$Fm){return
apply_queries("ALTER SCHEMA ".idf_escape($Fm)." TRANSFER",array_merge($S,$do));}function
trigger($A,$Q){if($A=="")return
array();$J=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($A));$H=reset($J);if($H)$H["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$H["text"]);return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($Q))as$I)$H[$I["name"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
routine($A,$T){$Ec=get_val("SELECT m.definition
FROM sys.objects o
JOIN sys.sql_modules m ON m.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($A)." AND o.type = ".q($T=="PROCEDURE"?"P":"FN"));if(!$Ec)return
array();$H=array("definition"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+(?=\S)~isU','',$Ec),"fields"=>array());foreach(get_rows("SELECT p.name, TYPE_NAME(p.user_type_id) [type], p.max_length, p.precision, p.scale, p.is_output
FROM sys.parameters p
JOIN sys.objects o ON p.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($A)."
ORDER BY p.parameter_id")as$I){$be=$I["type"];$x=type_length($be,$I);$n=array("field"=>preg_replace('~^@~','',$I["name"]),"type"=>$be,"length"=>$x,"full_type"=>$be.($x!=""?"($x)":""),"null"=>true,"inout"=>($I["is_output"]?"OUTPUT":""),);if($n["field"]=="")$H["returns"]=$n;else$H["fields"][]=$n;}return$H;}function
routines(){return
get_rows("SELECT o.name SPECIFIC_NAME, o.name ROUTINE_NAME,
	CASE o.type WHEN 'P' THEN 'PROCEDURE' ELSE 'FUNCTION' END ROUTINE_TYPE, TYPE_NAME(p.user_type_id) DTD_IDENTIFIER
FROM sys.objects o
LEFT JOIN sys.parameters p ON o.object_id = p.object_id AND p.parameter_id = 0
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('P', 'FN')
ORDER BY o.name");}function
routine_languages(){return
array();}function
routine_options($Lk){return
array();}function
routine_id($A,array$I){return
table($A);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($K,$h=null){$_GET["ns"]=$K;return!!get_val("SELECT 1 FROM sys.schemas WHERE name = ".q($K),0,$h);}function
schema_comment($K){return(string)get_val("SELECT CAST(value AS nvarchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', ".q($K).", NULL, NULL, NULL, NULL)");}function
set_schema_comment($K,$f){$dk="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($K);return(schema_comment($K)==""||queries("EXEC sp_dropextendedproperty $dk"))&&($f==""||queries("EXEC sp_addextendedproperty $dk, @value = ".q($f)));}function
create_sql($Q,$Na,$em){$O=table_status1($Q);if(is_view($O)){$co=view($Q);return"CREATE VIEW ".table($Q)." AS $co[select]";}$dk="EXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($Q);$Nb=($O["Comment"]!=""?";\n\n$dk, @value = ".q($O["Comment"]):"");$o=array();$Uj=false;foreach(fields($Q)as$A=>$n){$W=process_field($n,$n);if($W[6])$Uj=true;unset($W[5]);$o[]=implode("",$W);if($n["comment"]!="")$Nb
.=";\n\n$dk, @level2type = N'Column', @level2name = ".q($A).", @value = ".q($n["comment"]);}foreach(indexes($Q)as$A=>$u){if(!$Uj||$u["type"]!="PRIMARY"){$e=array();foreach($u["columns"]as$w=>$W)$e[]=idf_escape($W).($u["descs"][$w]?" DESC":"");$A=idf_escape($A);$o[]=($u["type"]=="INDEX"?"INDEX $A":"CONSTRAINT $A ".($u["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$A=>$rb)$o[]="CONSTRAINT ".idf_escape($A)." CHECK ($rb)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$o)."\n)$Nb";}function
foreign_keys_sql($Q){$o=array();foreach(foreign_keys($Q)as$re)$o[]=ltrim(format_foreign_key($re));return($o?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$o).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($uc,$em=""){return"USE ".idf_escape($uc);}function
use_schema_sql($K,$em){$A=idf_escape($K);$i="EXEC(".q("CREATE SCHEMA $A").")";$f=schema_comment($K);if($f!="")$i="BEGIN\n$i\nEXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($K).", @value = ".q($f)."\nEND";return($em=="DROP+CREATE"?"DROP SCHEMA IF EXISTS $A;\n":"")."IF SCHEMA_ID(".q($K).") IS NULL $i";}function
trigger_sql($Q){$H="";foreach(triggers($Q)as$A=>$jn)$H
.=create_trigger(" ON ".table($Q),trigger($A,$Q)).";";return$H;}function
convert_field(array$n){}function
unconvert_field(array$n,$H){return$H;}function
support($Xd){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|procedure|routine|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$Xd);}}add_driver("oracle","Oracle");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$M){return
url_host($M["host"]).($M["port"]!=""?":$M[port]":"").$M["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="OCI8";var$error_offset=0;private$link,$transaction=false;function
_error($_d,$m){$this->error=preg_replace('~^[^:]*: ~','',php_error_text($m));}function
attach(array$M,$U,$D){$this->link=@oci_new_connect($U,$D,easy_connect($M),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$m=oci_error();return($m?$m["message"]:lang(26));}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($uc){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($uc));}function
query($F,$vn=false){$G=oci_parse($this->link,$F);$this->error="";$this->error_offset=0;if(!$G){$m=oci_error($this->link);$this->errno=$m["code"];$this->error=$m["message"];return
false;}set_error_handler(array($this,'_error'));$H=@oci_execute($G,($this->transaction?OCI_NO_AUTO_COMMIT:OCI_COMMIT_ON_SUCCESS));restore_error_handler();if(!$H){$m=oci_error($G);$this->error_offset=($m?$m["offset"]:0);}if($H){if(oci_num_fields($G))return
new
Result($G);$this->affected_rows=oci_num_rows($G);oci_free_statement($G);}return$H;}function
timeout($Dh){return
function_exists('oci_set_call_timeout')&&oci_set_call_timeout($this->link,$Dh);}function
inTransaction(){return$this->transaction;}function
begin(){$this->transaction=true;return
true;}function
commit(){return$this->end_transaction(@oci_commit($this->link));}function
rollback(){return$this->end_transaction(@oci_rollback($this->link));}private
function
end_transaction($H){$this->transaction=false;if(!$H){$m=oci_error($this->link);$this->errno=$m["code"];$this->error=$m["message"];}return$H;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$w=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$I[$w]=$W->load();}return$I;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$H->native_type=idx(array(100=>"binary_float","binary_double"),$T,$T);return$H;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";function
attach(array$M,$U,$D){return$this->dsn("oci:dbname=//".easy_connect($M).";charset=AL32UTF8",$U,$D);}function
select_db($uc){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($uc));}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($om){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_object($g))$g->query("ALTER SESSION SET CURSOR_SHARING = FORCE"." NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"." NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'"." NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF TZH:TZM'");return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(30)=>array("date"=>19,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(31)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(32)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),lang(34)=>array("sdo_geometry"=>0),);}function
begin(){return$this->conn->begin();}function
convertSearch($t,array$W,array$n){$T=$n["type"];$zj=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $t AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($t)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($t, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$zj))return$t;return(!$zj&&preg_match(number_type(),$T)?$t:"TO_CHAR($t)");}function
quoteBinary($Uk){return"HEXTORAW(".q(bin2hex($Uk)).")";}function
typeName(\stdClass$n){return
strtolower(parent::typeName($n));}function
hasCStyleEscapes(){return
true;}function
errorLine($F){$B=(isset($this->conn->error_offset)?$this->conn->error_offset:0);return($B?substr_count($F,"\n",0,min($B,strlen($F)))+1:0);}function
tableHelp($A,$fg=false){if(DB=="SYS"){if(preg_match('~^G?V_\$(.+)~',$A,$_))return"refrn/V-$_[1].html";if(preg_match('~^(ALL|DBA|USER)_~',$A))return"refrn/$A.html";}}function
select($Q,array$L,array$Z,array$Me,array$Ki=array(),$y=1,$C=0,$Wj=false){if(in_array("*",$L)){$dc=array();$ve=false;foreach(fields($Q)as$A=>$n){$Ha=convert_field($n);$ve=($ve||$Ha);$dc[]=($Ha?"$Ha AS ":"").idf_escape($A);}if($ve)$L=$dc;}return
parent::select($Q,$L,$Z,$Me,$Ki,$y,$C,$Wj);}function
allFields(){$H=array();$J=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE '.where_owner("c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($J
as$I){$x="$I[precision],$I[scale]";$I["length"]=(strpos($I["type"],"(")?"":($x==","?$I["char_length"]:$x));$I["type"]=strtolower($I["type"]);$I["null"]=($I["nullable"]=="Y");$H[$I["tab"]][]=$I;}return$H;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($pe){$H=get_vals("SELECT username FROM all_users WHERE oracle_maintained = 'N' ORDER BY 1");return($H?:get_vals("SELECT username FROM all_users ORDER BY 1"));}function
limit($F,$Z,$y,$B=0,$ll=" "){return($B?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $F$Z) t WHERE rownum <= ".($y+$B).") WHERE rnum > $B":($y?" * FROM (SELECT $F$Z) WHERE rownum <= ".($y+$B):" $F$Z"));}function
limit1($Q,$F,$Z,$ll="\n"){return" $F$Z";}function
db_collation($k,array$Gb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
where_owner($aj="owner"){return"$aj = ".q(DB);}function
views_table($e){return"(SELECT $e FROM all_views WHERE ".where_owner().")";}function
objects_table(){return"(SELECT object_name, DECODE(object_type, 'VIEW', 'view', 'table') object_type FROM all_objects WHERE ".where_owner()." AND object_type IN ('TABLE', 'VIEW'))";}function
tables_list(){return
get_key_vals("SELECT * FROM ".objects_table()." ORDER BY 1");}function
count_tables(array$j){$H=array();foreach($j
as$k)$H[$k]=get_val("SELECT COUNT(*) FROM all_objects WHERE object_type IN ('TABLE', 'VIEW') AND owner = ".q($k));return$H;}function
table_status($A="",$Wd=false){$H=array();$al=q($A);if($Wd||$A!=""){foreach(get_rows('SELECT object_name "Name", object_type "Engine" FROM '.objects_table().($A!=""?" WHERE object_name = $al":"").' ORDER BY 1')as$I)$H[$I["Name"]]=$I;return$H;}foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE '.where_owner("t.owner")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM ".views_table("view_name")."
ORDER BY 1")as$I)$H[$I["Name"]]=$I;foreach(get_vals("SELECT table_name FROM all_indexes ai WHERE ".where_owner("table_owner")." AND (status = 'UNUSABLE'
	OR EXISTS (SELECT 1 FROM all_ind_partitions aip WHERE aip.index_owner = ai.owner AND aip.index_name = ai.index_name AND aip.status = 'UNUSABLE')
	OR EXISTS (SELECT 1 FROM all_ind_subpartitions ais WHERE ais.index_owner = ai.owner AND ais.index_name = ai.index_name AND ais.status = 'UNUSABLE'))")as$Q){if($H[$Q])$H[$Q]["invalid_index"]="1";}return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$H=array();$tf=null;foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)." AND ".where_owner()." ORDER BY column_id")as$I){$T=$I["DATA_TYPE"];$x="$I[DATA_PRECISION],$I[DATA_SCALE]";if($x==","){$x=($T=="RAW"?$I["DATA_LENGTH"]:(preg_match('~CHAR~',$T)?$I["CHAR_COL_DECL_LENGTH"]:""));if($I["CHAR_USED"]=="C"&&preg_match('~^(VARCHAR2|CHAR)$~',$T))$x="$I[CHAR_LENGTH] CHAR";}elseif(strpos($T,"("))$x="";$l=$I["DATA_DEFAULT"];$Bc=false;if($l!==null){$l=rtrim($l);if(preg_match("~^'(?:[^']|'')*'\$~s",$l))$l=idf_unescape($l);elseif(preg_match('~^NULL$~i',$l))$l=null;else$Bc=!is_numeric($l);}if($I["IDENTITY_COLUMN"]=="YES"){if($tf===null)$tf=get_key_vals("SELECT column_name, generation_type FROM all_tab_identity_cols WHERE table_name = ".q($Q)." AND ".where_owner());$l="GENERATED ".$tf[$I["COLUMN_NAME"]].($I["DEFAULT_ON_NULL"]=="YES"?" ON NULL":"")." AS IDENTITY";}$Zj=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($I["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$Zj["where"]=1;$H[$I["COLUMN_NAME"]]=array("field"=>$I["COLUMN_NAME"],"full_type"=>$T.($x?"($x)":""),"type"=>strtolower($T),"length"=>$x,"default"=>$l,"default_sql"=>($Bc&&$I["IDENTITY_COLUMN"]!="YES"),"null"=>($I["NULLABLE"]=="Y"),"auto_increment"=>($I["IDENTITY_COLUMN"]=="YES"),"privileges"=>$Zj,);}return$H;}function
table_constraints($Q,$h=null){$H=array();foreach(get_rows('SELECT c.constraint_name "name", c.constraint_type "type", c.r_owner "r_owner", c.r_constraint_name "r_constraint", c.delete_rule "delete_rule", cc.column_name "column"
FROM all_constraints c
JOIN all_cons_columns cc ON cc.owner = c.owner AND cc.constraint_name = c.constraint_name
WHERE c.constraint_type IN (\'P\', \'U\', \'R\') AND '.where_owner("c.owner")." AND c.table_name = ".q($Q).'
ORDER BY cc.position',$h)as$I){$A=$I["name"];$H[$A]["type"]=$I["type"];$H[$A]["r_owner"]=$I["r_owner"];$H[$A]["r_constraint"]=$I["r_constraint"];$H[$A]["delete_rule"]=$I["delete_rule"];$H[$A]["columns"][]=$I["column"];}return$H;}function
indexes($Q,$h=null){$H=array();$Xb=array();$pj=array();foreach(table_constraints($Q,$h)as$A=>$Wb)$Xb[$A]=$Wb["type"];foreach(get_rows("SELECT aic.*, atc.data_default, ai.status, ai.partitioned
FROM all_ind_columns aic
JOIN all_indexes ai ON ai.owner = aic.index_owner AND ai.index_name = aic.index_name
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)." AND ".where_owner("aic.table_owner")."
ORDER BY aic.column_position",$h)as$I){$Ff=$I["INDEX_NAME"];$Jb=$I["DATA_DEFAULT"];$Jb=($Jb?trim($Jb,'"'):$I["COLUMN_NAME"]);$T=idx($Xb,$Ff);$H[$Ff]["type"]=($T=="P"?"PRIMARY":($T=="U"?"UNIQUE":"INDEX"));$H[$Ff]["invalid"]=($I["STATUS"]=="UNUSABLE");$H[$Ff]["columns"][]=$Jb;$H[$Ff]["lengths"][]=($I["CHAR_LENGTH"]&&$I["CHAR_LENGTH"]!=$I["COLUMN_LENGTH"]?$I["CHAR_LENGTH"]:null);$H[$Ff]["descs"][]=($I["DESCEND"]&&$I["DESCEND"]=="DESC"?'1':null);if($I["PARTITIONED"]=="YES")$pj[$Ff]=q($Ff);}if($pj){$zf=where_owner("index_owner")." AND index_name IN (".implode(", ",$pj).") AND status = 'UNUSABLE'";foreach(get_rows("SELECT index_name FROM all_ind_partitions WHERE $zf UNION SELECT index_name FROM all_ind_subpartitions WHERE $zf",$h)as$I)$H[$I["INDEX_NAME"]]["invalid"]=true;}uasort($H,function($ia,$Ra){$Ki=array("PRIMARY"=>0,"UNIQUE"=>1,"INDEX"=>2);return$Ki[$ia["type"]]-$Ki[$Ra["type"]];});return$H;}function
view($A){$J=get_rows('SELECT text "select" FROM '.views_table("view_name, text").' WHERE view_name = '.q($A));return($J?$J[0]:array());}function
collations(){return
array();}function
information_schema($k,$K=""){return
in_array($K!=""?$K:$k,array("INFORMATION_SCHEMA","SYS","SYSTEM"));}function
error(){return
h(connection()->error);}function
explain(Db$g,$F){$g->query("EXPLAIN PLAN FOR $F");return$g->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$A,array$o,array$re,$f,$vd,$Fb,$Na,$qj){$b=$cd=array();$Si=($Q?fields($Q):array());foreach($o
as$n){$W=$n[1];if($W&&$n[0]!=""&&idf_escape($n[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($n[0])." TO $W[0]");$Ri=$Si[$n[0]];if($W&&$Ri){$qi=process_field($Ri,$Ri);if($W[2]==$qi[2])$W[2]="";}if($W){list($W[2],$W[3])=array($W[3],$W[2]);$b[]=($Q!=""?($n[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");}else$cd[]=idf_escape($n[0]);}if($Q=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",array_merge($b,$re))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$cd||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$cd).")"))&&($Q==$A||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($A)));}function
alter_indexes($Q,$b){$cd=array();$hk=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$i=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($hk,"ALTER TABLE ".table($Q).$i);}elseif($W[2]=="DROP")$cd[]=idf_escape($W[1]);else$hk[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($cd)array_unshift($hk,"DROP INDEX ".implode(", ",$cd));foreach($hk
as$F){if(!queries($F))return
false;}return
true;}function
foreign_keys($Q){$H=array();$Im=array();foreach(table_constraints($Q)as$A=>$Wb){if($Wb["type"]=="R"){$H[$A]=array("source"=>$Wb["columns"],"target"=>array(),"on_delete"=>$Wb["delete_rule"],"on_update"=>null,);$Im[$A]=array($Wb["r_owner"],$Wb["r_constraint"]);}}if($Im){$Z=array();foreach($Im
as$Fm)$Z[]="(owner = ".q($Fm[0])." AND constraint_name = ".q($Fm[1]).")";foreach(get_rows("SELECT owner, constraint_name, table_name, column_name FROM all_cons_columns WHERE ".implode(" OR ",array_unique($Z))." ORDER BY position")as$I){foreach($Im
as$A=>$Fm){if($Fm==array($I["OWNER"],$I["CONSTRAINT_NAME"])){$H[$A]["db"]=$I["OWNER"];$H[$A]["table"]=$I["TABLE_NAME"];$H[$A]["target"][]=$I["COLUMN_NAME"];}}}}return$H;}function
trigger($A,$Q){if($A=="")return
array();$J=get_rows('SELECT trigger_name "Trigger", trigger_type "Type", triggering_event "Event", trigger_body "Statement"
FROM all_triggers
WHERE trigger_name = '.q($A)." AND ".where_owner());$H=reset($J);if($H){$T=$H["Type"];$H["Timing"]=(preg_match('~^(BEFORE|AFTER|INSTEAD OF)~',$T,$_)?$_[1]:$T);$H["Type"]=(preg_match('~EACH ROW~',$T)||$T=="INSTEAD OF"?"FOR EACH ROW":"");}return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SELECT trigger_name, trigger_type, triggering_event FROM all_triggers WHERE table_name = ".q($Q)." AND ".where_owner())as$I)$H[$I["TRIGGER_NAME"]]=array(preg_replace('~ (STATEMENT|EACH ROW)$~','',$I["TRIGGER_TYPE"]),$I["TRIGGERING_EVENT"]);return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE","INSERT OR UPDATE","INSERT OR DELETE","UPDATE OR DELETE","INSERT OR UPDATE OR DELETE"),"Type"=>array("FOR EACH ROW",""),);}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$do){return
apply_queries("DROP VIEW",$do);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($G){return"0";}function
create_database($k,$Fb){$H=queries("CREATE USER ".idf_escape($k)." NO AUTHENTICATION");return($H?queries("GRANT UNLIMITED TABLESPACE TO ".idf_escape($k)):$H);}function
drop_databases(array$j){$H=true;foreach($j
as$k)$H=!!queries("DROP USER ".idf_escape($k)." CASCADE")&&$H;return$H;}function
rename_database($A,$Fb){return!!queries("ALTER USER ".idf_escape(DB)." RENAME TO ".idf_escape($A));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$H=array();$J=get_rows('SELECT * FROM v$instance');foreach(reset($J)as$w=>$W)$H[]=array($w,$W);return$H;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess
LEFT JOIN v$sql sql ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$n){if($n["type"]=="sdo_geometry")return"SDO_UTIL.TO_WKTGEOMETRY(".idf_escape($n["field"]).")";}function
unconvert_field(array$n,$H){return($n["type"]=="sdo_geometry"?"SDO_UTIL.FROM_WKTGEOMETRY($H)":$H);}function
support($Xd){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|sql|status|table|trigger|variables|view|view_trigger)$~',$Xd);}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+584b178")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($i=false){return
password_file($i);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($M){return
h($M);}function
database(){return
DB;}function
databases($pe=true){return
get_databases($pe);}function
pluginsLinks(){}function
operators($om=null){return
driver()->operators($om);}function
schemas(){$H=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$H))array_unshift($H,$_GET["ns"]);return$H;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$lc){return$lc;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$of=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$jl=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($of!=""?" - $of":""),'short_name'=>'Adminer','description'=>lang(38),'start_url'=>$jl,'scope'=>$jl,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+584b178",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($qc=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$H=array();foreach(array("","-dark")as$zh){$p="adminer$zh.css";if(file_exists($p)){$de=file_get_contents($p);$H["$p?v=".crc32($de)]=($zh?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$de)?'':'light'));}}return$H;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(39).'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,on('change','loginDriver'))),adminer()->loginFormField('server','<tr><th>'.lang(40).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(41)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(42).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("fire(qs('#username').form['auth[driver]'], 'change');")),adminer()->loginFormField('password','<tr><th>'.lang(43).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(44).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(45)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(46))."\n";}function
loginFormField($A,$ff,$X){return$ff.$X."\n";}function
login($Pg,$D){if($D=="")return
lang(47).require_password_link(null);if(!Driver::$passwords)return
lang(48).require_password_link($D);if(!password_required())return
lang(49).require_password_link($D);return
true;}function
tableName(array$om){return
h($om["Name"]);}function
fieldName(array$n,$Ki=0){$T=$n["full_type"].($n["null"]?" NULL":"");$f=$n["comment"];return'<span title="'.h($T.($f!=""?($T?": ":"").$f:'')).'">'.h($n["field"]).'</span>';}function
commentValue($T,$f){if($f==""||$T=='SCHEMA'||$T=='TABLE'||$T=='COLUMN')return
h($f);$Nj=function($Uk,$kb='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$kb>",preg_replace('~\|$~m',"",rtrim($Uk))));};$Q='(\+--[-+]+\+\n)';$I='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$Q?$I$Q?($I*)$Q?~m",function($_)use($Nj){return"<table>\n".($_[1]?"<thead>".$Nj($_[2],'th')."<tbody>\n":$Nj($_[2])).$Nj($_[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($f))))."</pre>\n";}function
commentInput($T,$c,$f){$X=h($f);return(preg_match('~\n~',$X)?"<textarea$c rows='2' cols='".($T=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");}function
selectLinks(array$om,$N=""){$A=$om["Name"];echo'<p class="links">';$Kg=array();if($A!="")$Kg["select"]=lang(50);if(support("table")||support("indexes"))$Kg["table"]=lang(51);if(support("table")){if(is_view($om)){if(support("view"))$Kg["view"]=lang(52);}elseif(function_exists('Adminer\alter_table')&&$A!="")$Kg["create"]=lang(53);}if($N!==null)$Kg["edit"]=lang(54);foreach($Kg
as$w=>$W)echo" <a href='".h(ME)."$w=".url_escape($A).($w=="edit"?$N:"")."'".bold(isset($_GET[$w])).">$W</a>";echo"\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$nm){return
array();}function
backwardKeysPrint(array$Ta,array$I){}function
selectQuery($F,$Wl,$Vd=false){$H="\n";if(!$Vd&&($ho=driver()->warnings())){$s="warnings";$H=", <a href='#$s' class='toggle'>".lang(55)."</a>"."$H<div id='$s' class='hidden'>\n$ho</div>\n";}return"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>(".format_time($Wl).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($F)."' class='hover'>".lang(14)."</a>":"").$H;}function
sqlCommandQuery($F){return
shorten_utf8(trim($F),1000);}function
sqlPrintAfter(){}function
sqlUrlLength(){return
2000;}function
explain(Db$g,$F,array$Oi){$G=explain($g,$F);if(!$G)return"";ob_start();print_select_result($G,$g,$Oi);return
ob_get_clean();}function
rowDescription($Q){return"";}function
rowDescriptions(array$J,array$se){return$J;}function
selectLink($W,array$n){}function
selectVal($W,$z,array$n,$Vi){$H=($W===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$n["type"])&&!preg_match("~var~",$n["type"])?"<code>$W</code>":(preg_match('~^jsonb?$~',$n["full_type"])?"<code class='jush-json'>$W</code>":$W)));if(is_blob($n)&&!is_utf8($W))$H="<i>".lang(56,strlen($Vi))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$H</a>":$H);}function
editVal($W,array$n){return$W;}function
config(){return
array();}function
tableStructurePrint(array$o,$om=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(57)."<th>".lang(58).(support("comment")?"<th>".lang(59):"")."<tbody>\n";$Sn=(support("type")?types():array());foreach($o
as$n){echo"<tr><th>".h($n["field"]).doc_link(array(JUSH=>$n["doc"]));$T=h($n["full_type"]);$Fb=h($n["collation"]);echo"<td><span title='$Fb'>".(in_array($T,$Sn)?"<a href='".h(ME.'type='.url_escape($T))."'>$T</a>":$T.($Fb&&isset($om["Collation"])&&$Fb!=$om["Collation"]?" $Fb":""))."</span>",($n["null"]?" <i>NULL</i>":""),($n["auto_increment"]?" <i>".lang(60)."</i>":""),(isset($n["default"])?" <span title='".lang(61)."'>[<b>".($n["generated"]||$n["default_sql"]?"<code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($n["default"])),80,"</code>"):h($n["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$n["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$om){$kj=false;foreach($v
as$A=>$u)$kj|=!!$u["partial"];echo"<table>\n";$_c=first(driver()->indexAlgorithms($om));foreach($v
as$A=>$u){ksort($u["columns"]);$Wj=array();foreach($u["columns"]as$w=>$W)$Wj[]="<i>".h($W)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($A)."'>","<th>".h($u["type"]).($_c&&$u['algorithm']!=$_c?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$Wj).($u["invalid"]?" <span class='error'>INVALID</span>":"");if($kj)echo"<td>".($u['partial']?"<code class='sql jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($T){if($T=="FOREIGN"||$T=="CHECK")return"";if($T=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($c,array$e,$X="",$Cj=""){return
select_input($c,$e,$X,$Cj);}function
selectColumnsPrint(array$L,array$e){print_fieldset("select",lang(62),$L);$r=0;$L[""]=array();foreach($L
as$w=>$W){$W=idx($_GET["columns"],$w,array());$d=adminer()->selectColumnInput(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$e,$W["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(63)=>driver()->functions,lang(64)=>driver()->grouping)),$W["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($d)":$d)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$v,$om=null){print_fieldset("search",lang(65),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$Di=adminer()->operators($om);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$W){if(!$W||(("$W[col]$W[val]"!=""||preg_match('~NULL$~',$W["op"]))&&in_array($W["op"],$Di)))echo"<div>".adminer()->selectColumnInput(" name='where[$r][col]' data-default=''".on('change',($W?'selectFieldChange':'selectAddRow')),$e,$W["col"],"(".lang(66).")"),html_select("where[$r][op]",$Di,$W["op"]," data-default='".h(first($Di))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($W["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$Ki,array$e,array$v){print_fieldset("sort",lang(67),$Ki);$r=0;foreach((array)$_GET["order"]as$w=>$W){if($W!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$e,$W),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(68))."</div>\n";$r++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectAddRow'),$e),checkbox("desc[$r]",1,false,lang(68))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(69)."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($Mm){echo"<fieldset><legend>".lang(70)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Mm)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(71)."</legend><div>","<input type='submit' value='".lang(62)."'>"," <span id='noindex' title='".lang(72)."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($v
as$u){$pc=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$pc)$e[$pc]=1;}$e[""]=1;foreach($e
as$w=>$W)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$qd,array$e){}function
selectColumnsProcess(array$e,array$v){$L=array();$Me=array();foreach((array)$_GET["columns"]as$w=>$W){if($W["fun"]=="count"||($W["col"]!=""&&(!$W["fun"]||in_array($W["fun"],driver()->functions)||in_array($W["fun"],driver()->grouping)))){$L[$w]=apply_sql_function($W["fun"],($W["col"]!=""?idf_escape($W["col"]):"*"));if(!in_array($W["fun"],driver()->grouping))$Me[]=$L[$w];}}return
array($L,$Me);}function
selectSearchProcess(array$o,array$v,$om=null){$H=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$H[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$Di=adminer()->operators($om);foreach((array)$_GET["where"]as$w=>$W){$W+=array("col"=>"","op"=>first($Di),"val"=>"");$_GET["where"][$w]=$W;$Db=$W["col"];if(("$Db$W[val]"!=""||preg_match('~NULL$~',$W["op"]))&&in_array($W["op"],$Di)){if($W["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Qb=array();foreach(($Db!=""?array($Db=>idx($o,$Db,array())):$o)as$A=>$n){if($Db!=""||is_searchable($n,$W))$Qb[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($A),$W,$n),$W["op"],$W["val"]);}$H[]=(count($Qb)==1?$Qb[0]:($Qb?"(".implode(" OR ",$Qb).")":"1 = 0"));}}return$H;}function
selectSearchCondition($d,$_i,$W){if(preg_match('~IN$~',$_i))return"$d $_i ".($W!=""?process_in($W):"(NULL)");if($_i=="BETWEEN")return"$d $_i ".process_between($W);if($_i=="SQL")return"$d $W";if(preg_match('~^(I?LIKE) %%$~',$_i,$_))return"$d $_[1] ".q("%$W%");if($_i=="FIND_IN_SET")return"$_i(".q($W).", $d)";return"$d $_i".(!preg_match('~NULL$~',$_i)?" ".q($W):"");}function
selectOrderProcess(array$o,array$v){$H=array();foreach((array)$_GET["order"]as$w=>$W){if($W!="")$H[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$W)?$W:idf_escape($W)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($o[$W],"null")?" NULLS LAST":""):"");}return$H;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$se){return
false;}function
selectQueryBuild(array$L,array$Z,array$Me,array$Ki,$y,$C){return"";}function
messageQuery($F,$Om,$Vd=false){restart_session();$lf=&get_session("queries");if(!idx($lf,$_GET["db"]))$lf[$_GET["db"]]=array();if(strlen($F)>1e6)$F=preg_replace('~[\x80-\xFF]+$~','',substr($F,0,1e6))."\n…";$lf[$_GET["db"]][]=array($F,time(),$Om);$Rl="sql-".count($lf[$_GET["db"]]);$H="<a href='#$Rl' class='toggle'>".lang(73)."</a> ".copy_icon()."\n";if(!$Vd&&($ho=driver()->warnings())){$s="warnings-".count($lf[$_GET["db"]]);$H="<a href='#$s' class='toggle'>".lang(55)."</a>, $H<div id='$s' class='hidden'>\n$ho</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $H<div id='$Rl' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($F,1e4)."</code></pre>".($Om?" <span class='time'>($Om)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($lf[$_GET["db"]])-1)).'">'.lang(14).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($Q,array$o,$I,$Fn,$F='',$Om=''){echo($F!=""?"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>($Om)</span>\n":"");}function
editFunctions(array$n){$H=($n["null"]?"NULL/":"");$bf=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$Ge){if(!$w||(!isset($_GET["call"])&&$bf)){foreach($Ge
as$zj=>$W){if(!$zj||preg_match("~$zj~",$n["type"]))$H
.="/$W";}}if($w&&$Ge&&!preg_match('~set|bool~',$n["type"])&&!is_blob($n))$H
.="/SQL";}if($n["auto_increment"]&&!$bf)$H=lang(60);return
explode("/",$H);}function
editInput($Q,array$n,$c,$X){if($n["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$c,$n,$X,"NULL");return"";}function
editHint($Q,array$n,$X){return"";}function
processInput(array$n,$X,$Fe=""){if($Fe=="SQL")return$X;$A=$n["field"];$H=q($X);if(preg_match('~^(now|getdate|uuid)$~',$Fe))$H="$Fe()";elseif(preg_match('~^current_(date|timestamp)$~',$Fe))$H=$Fe;elseif(preg_match('~^([+-]|\|\|)$~',$Fe))$H=idf_escape($A)." $Fe $H";elseif(preg_match('~^[+-] interval$~',$Fe))$H=idf_escape($A)." $Fe ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$X)&&JUSH!="pgsql"?$X:$H);elseif(preg_match('~^(addtime|subtime|concat)$~',$Fe))$H="$Fe(".idf_escape($A).", $H)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$Fe))$H="$Fe($H)";return
unconvert_field($n,$H);}function
dumpOutput(){$H=array('text'=>lang(74),'file'=>lang(75));if(function_exists('gzencode'))$H['gz']='gzip';return$H;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($k){}function
dumpTable($Q,$em,$fg=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($em)dump_csv(array_keys(fields($Q)));}else{if($fg==2){$o=array();foreach(fields($Q)as$A=>$n)$o[]=idf_escape($A)." ".full_type_sql($n);$i="CREATE TABLE ".table($Q)." (".implode(", ",$o).")";}else$i=create_sql($Q,$_POST["auto_increment"],$em);set_utf8mb4($i);if($em&&$i){if(($em=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$fg==1)echo"DROP ".($fg==2?"VIEW":"TABLE")." IF EXISTS ".table($Q).";\n";if($fg==1)$i=remove_definer($i);echo"$i;\n\n";}}}function
dumpData($Q,$em,$F,array$L=array(),array$Z=array(),array$Me=array(),array$Ki=array()){if($em){$ah=(JUSH=="sqlite"?0:1048576);$o=array();$uf=false;if($_POST["format"]=="sql"){if($em=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($Q).";\n";$o=fields($Q);if(JUSH=="mssql"){foreach($o
as$n){if($n["auto_increment"]){echo"SET IDENTITY_INSERT ".table($Q)." ON;\n";$uf=true;break;}}}}$G=($F!=""?connection()->query($F,1):driver()->select($Q,($L?:array("*")),$Z,$Me,$Ki,0));if($G){$Qf="";$eb="";$ng=array();$He=array();$gm="";$Yd=($Q!=''?'fetch_assoc':'fetch_row');$hc=0;while($I=$G->$Yd()){if(!$ng){$Y=array();foreach($I
as$W){$n=$G->fetch_field();if(idx($o[$n->name],'generated')){$He[$n->name]=true;continue;}$ng[]=$n->name;$w=idf_escape($n->name);$Y[]="$w = VALUES($w)";}$gm=($em=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Y):"").";\n";}foreach($I
as$w=>$W){if(is_array($W))$I[$w]=json_encode($W,256|64);}if($_POST["format"]!="sql"){if($em=="table"){dump_csv($ng);$em="INSERT";}dump_csv($I);}else{if(!$Qf)$Qf="INSERT INTO ".table($Q)." (".implode(", ",array_map('Adminer\idf_escape',$ng)).") VALUES";foreach($I
as$w=>$W){if($He[$w]){unset($I[$w]);continue;}$n=$o[$w];$I[$w]=($W===null?"NULL":($W===false?0:unconvert_field($n,preg_match(number_type(),$n["type"])&&!preg_match('~\[~',$n["full_type"])&&is_numeric($W)?$W:(!is_blob($n)||is_utf8($W)?q($W):driver()->quoteBinary($W)))));}$Uk=($ah?"\n":" ")."(".implode(",\t",$I).")";if(!$eb)$eb=$Qf.$Uk;elseif(JUSH=='mssql'?$hc%1000!=0:strlen($eb)+4+strlen($Uk)+strlen($gm)<$ah)$eb
.=",$Uk";else{echo$eb.$gm;$eb=$Qf.$Uk;}}$hc++;}if($eb)echo$eb.$gm;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
dump_csv(array(connection()->error));if($uf)echo"SET IDENTITY_INSERT ".table($Q)." OFF;\n";}}function
dumpFilename($sf){return
friendly_url($sf!=""?$sf:(SERVER?:"localhost"));}function
dumpHeaders($sf,$Fh=false){$Zi=$_POST["output"];$Qd=(preg_match('~sql~',$_POST["format"])?"sql":($Fh?"tar":"csv"));header("Content-Type: ".($Zi=="gz"?"application/x-gzip":($Qd=="tar"?"application/x-tar":($Qd=="sql"||$Zi!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Zi=="gz"){ob_start(function($P){return
gzencode($P);},1e6);}return$Qd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(76)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(77):lang(78))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(79)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(80)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(81)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(82)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(83)."</a>\n":"");return
true;}function
navigation($yh){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Zh=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Zh)<0?h($Zh):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($yh=="auth"){$Zi="";foreach((array)$_SESSION["pwds"]as$ao=>$yl){foreach($yl
as$M=>$Tn){$A=h(get_setting("vendor-$ao-$M")?:get_driver($ao));foreach($Tn
as$U=>$D){if($A&&$D!==null){$yc=$_SESSION["db"][$ao][$M][$U];foreach(($yc?array_keys($yc):array(""))as$k)$Zi
.="<li><a href='".h(auth_url($ao,$M,$U,$k))."'>($A) ".h("$U@").($M!=""?adminer()->serverName($M):"").h($k!=""?" - $k":"")."</a>\n";}}}}if($Zi)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Zi</ul>\n";}else{$S=array();if($_GET["ns"]!==""&&!$yh&&DB!=""){connection()->select_db(DB);$S=table_status('',true);}adminer()->syntaxHighlighting($S);adminer()->databasesPrint($yh);$la=array();if(DB==""||!$yh){if(support("sql")){$la['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(73)."</a>";$la['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(84)."</a>";}$la['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(85)."</a>";}$_f=$_GET["ns"]!==""&&!$yh&&DB!="";if($_f&&function_exists('Adminer\alter_table'))$la['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(86)."</a>";$la=adminer()->menuActions($la,$yh);echo($la?"<p class='links'>\n".implode("\n",$la)."\n":"");if($_f){if($S)adminer()->tablesPrint($S);else
echo"<p class='message'>".lang(13)."</p>\n";}}}function
syntaxHighlighting(array$S){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.2.0+584b178",true);$Ah=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Ah?script("addEventListener('DOMContentLoaded', () => {\n$Ah\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($S){$Kg=array();foreach($S
as$Q=>$T)$Kg[]=js_escape_re($Q);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Kg).')(?!\$)\b/g',false);$Tl=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($Tl))){foreach(routines()as$I)json_row(js_escape(ME).'function='.url_escape($I["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($I["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$W)echo"jushLinks.$W = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$Zl=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Pa=Driver::jushAutocomplete($S,$Zl);echo($Pa?"addEventListener('DOMContentLoaded', () => { autocompleter = $Pa; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($yh){if(support("single_db"))return;$j=adminer()->databases();if(DB&&$j&&!in_array(DB,$j))array_unshift($j,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$vc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(44)."'>".lang(87).": ".($j?html_select("db",array(""=>"")+group_system($j),DB,$vc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(25)."'".($j?" class='hidden'":"").">\n";if(support("scheme")){if($yh!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".lang(88).": ".html_select("ns",array(""=>"")+group_system(adminer()->schemas(),true),$_GET["ns"],$vc)."</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$W){if(isset($_GET[$W])){echo
input_hidden($W);break;}}echo"</p></form>\n";}function
menuActions(array$la,$yh){return$la;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$Q=>$O){$Q="$Q";$A=adminer()->tableName($O);if($A!=""&&!$O["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($Q).'"'.bold($_GET["select"]==$Q||$_GET["edit"]==$Q,"select hover")." title='".lang(50)."'>".lang(89)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($Q).'"'.bold(in_array($Q,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($O)?"view":"structure"))." title='".lang(51)."'>$A</a>":"<span>$A</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}function
formatSizeValue($W){return
format_number($W);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$disabled=array();var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Gj){$bd=SqlDriver::$drivers;$hf=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Gj===null){$Gj=array();$Xa="adminer-plugins";if(is_dir($Xa)){foreach(glob("$Xa/*.php")as$p){$ee=SqlDriver::$drivers;$this->includeOnce($p);foreach(array_diff_key(SqlDriver::$drivers,$ee)as$s=>$A)$this->driverFiles[$s]=$p;}}if(file_exists("$Xa.php")){$Bf=$this->includeOnce("$Xa.php");if(is_array($Bf)){foreach($Bf
as$w=>$Dj)$Gj[is_object($Dj)?get_class($Dj):$w]=$Dj;}else$this->error
.=lang(90,"<b>$Xa.php</b>",$hf)."<br>";}foreach(get_declared_classes()as$_b){if(!$Gj[$_b]&&(preg_match('~^Adminer\w~i',$_b)||is_subclass_of($_b,'Adminer\Plugin'))){$uk=new
\ReflectionClass($_b);$Yb=$uk->getConstructor();if($Yb&&$Yb->getNumberOfRequiredParameters())$this->error
.=lang(91,$hf,"<b>$_b</b>","<b>$Xa.php</b>")."<br>";else$Gj[$_b]=new$_b;}}}$Vf=array_filter($Gj,function($Dj){return!is_object($Dj);});if($Vf){$this->error
.=lang(92,$hf)."<br>";$Gj=array_diff_key($Gj,$Vf);}$km=get_setting("plugins","adminer_config");foreach($Gj
as$w=>$Dj){if($Dj
instanceof
Plugin&&$Dj::$optional){$Xl=idx($km,get_class($Dj));if($Xl!==null?!$Xl:!$Dj->enabled){$this->disabled[]=$Dj;unset($Gj[$w]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$bd);$this->plugins=$Gj;$qa=new
Adminer;$Gj[]=$qa;$uk=new
\ReflectionObject($qa);foreach($uk->getMethods()as$vh){foreach($Gj
as$Dj){$A=$vh->getName();if(method_exists($Dj,$A))$this->hooks[$A][]=$Dj;}}}function
includeOnce($p){return
include_once"./$p";}static
function
describe($Dj){$Jc=(method_exists($Dj,'description')?$Dj->description():"");if(!$Jc){$uk=new
\ReflectionObject($Dj);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$uk->getDocComment(),$_))$Jc=$_[1];}$Zk=(method_exists($Dj,'screenshot')?$Dj->screenshot():"");return
h($Jc).($Zk?($Jc?" ":"")."(<a href='".h($Zk)."'".target_blank().">".lang(93)."</a>)":"");}static
function
checksum($p){$de=str_replace("\r","",file_get_contents($p));$de=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$de);return
dechex(crc32($de));}function
checksums(){$fe=array_values($this->driverFiles);foreach($this->plugins
as$Dj){$uk=new
\ReflectionObject($Dj);$fe[]=$uk->getFileName();}$H=array();foreach($fe
as$p)$H[basename($p,'.php')]=self::checksum($p);return$H;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'bdac2201','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'5903cccc','dump-alter'=>'896b579e','dump-bz2'=>'c9229da6','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'735d7e2','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'10add0e8','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'ec1da606','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'89783b50','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'267580a3','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'577c07b2','select-image'=>'39ed1607','slugify'=>'7b422f0e','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'51a3d7d5','elastic'=>'9b83a12a','firebird'=>'5f3d9493','igdb'=>'3b00190b','imap'=>'3ba195cd','mongo'=>'235e3ba','redis'=>'41709f61','simpledb'=>'b8e2cc7d',);}function
__call($A,array$gj){$Fa=array();foreach($gj
as$w=>$W)$Fa[]=&$gj[$w];$H=null;foreach($this->hooks[$A]as$Dj){$X=call_user_func_array(array($Dj,$A),$Fa);if($X!==null){if(!self::$append[$A])return$X;$H=$X+(array)$H;}}return$H;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=array();function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$hi=null){$Fa=func_get_args();$Fa[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$Fa);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($vj){$this->password_hash=$vj;}function
description(){return
lang(94);}function
credentials(){$D=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($D)&&!password_required()?"":$D));}function
login($Pg,$D){if($this->passwordMatches($D))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($D){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($D),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$M,$U,$D){mysqli_report(MYSQLI_REPORT_OFF);$Hj=$M["port"];$sd=("$M[host]$Hj$M[socket]"=="");$Vl=adminer()->connectSsl();$Pn=($Vl&&($Vl['key']||$Vl['cert']||$Vl['ca']||isset($Vl['verify'])));if($Pn)$this->ssl_set($Vl['key'],$Vl['cert'],$Vl['ca'],'','');$H=@$this->real_connect((!$sd?$M["host"]:ini_get("mysqli.default_host")),(!$sd||$U!=""?$U:ini_get("mysqli.default_user")),(!$sd||$U.$D!=""?$D:ini_get("mysqli.default_pw")),null,($Hj!=""?intval($Hj):ini_get("mysqli.default_port")),($Hj!=""?null:$M["socket"]),($Pn?($Vl['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($H?'':$this->error);}function
set_charset($pb){if(parent::set_charset($pb))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $pb");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$M,$U,$D){if(ini_bool("mysql.allow_local_infile"))return
lang(95,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Hj="$M[port]$M[socket]";$A=$M["host"].($Hj!=""?":$Hj":"");$this->link=@mysql_connect(($A!=""?$A:ini_get("mysql.default_host")),($A.$U!=""?$U:ini_get("mysql.default_user")),($A.$U.$D!=""?$D:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($pb){return
mysql_set_charset($pb,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($uc){return
mysql_select_db($uc,$this->link);}function
query($F,$vn=false){$G=@($vn?mysql_unbuffered_query($F,$this->link):mysql_query($F,$this->link));$this->error="";if(!$G){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($G===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($G);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=mysql_num_rows($G);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$H=mysql_fetch_field($this->result,$this->offset++);$H->orgtable=$H->table;$H->native_type=idx(array("string"=>"varchar","real"=>"double"),$H->type,$H->type);return$H;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$M,$U,$D){$Hi=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$Hi[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$Vl=adminer()->connectSsl();if($Vl){if($Vl['key'])$Hi[\PDO::MYSQL_ATTR_SSL_KEY]=$Vl['key'];if($Vl['cert'])$Hi[\PDO::MYSQL_ATTR_SSL_CERT]=$Vl['cert'];if($Vl['ca'])$Hi[\PDO::MYSQL_ATTR_SSL_CA]=$Vl['ca'];if(isset($Vl['verify']))$Hi[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Vl['verify'];}$of=$M["host"];$Hj=$M["port"];$Hl=$M["socket"];return$this->dsn("mysql:charset=utf8".($of!=""?";host=$of":'').($Hj!=""?";port=$Hj":($Hl!=""?";unix_socket=$Hl":"")),$U,$D,$Hi);}function
set_charset($pb){return$this->query("SET NAMES $pb");}function
select_db($uc){return$this->query("USE ".idf_escape($uc));}function
query($F,$vn=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$vn);return
parent::query($F,$vn);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($om){return
array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_string($g)){if(function_exists('iconv')&&!is_utf8($g)&&strlen($Uk=iconv("windows-1252","utf-8//IGNORE",$g))>strlen($g))$g=$Uk;return$g;}$g->set_charset(charset($g));$g->query("SET sql_quote_show_create = 1, autocommit = 1");$g->flavor=(preg_match('~MariaDB~',$g->server_info)?'maria':'mysql');add_driver(DRIVER,($g->flavor=='maria'?"MariaDB":"MySQL"));return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(30)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(31)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(96)=>array("enum"=>65535,"set"=>64),lang(32)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(34)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$g))$this->types[lang(31)]["json"]=4294967295;if(min_version('',10.7,$g)){$this->types[lang(31)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$g)){$this->types[lang(33)]["inet6"]=39;if(min_version('','10.10',$g))$this->types[lang(33)]["inet4"]=15;}if(min_version(9,11.7,$g))$this->types[lang(29)]["vector"]=16383;if(min_version(5.7,10.2,$g))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$n){return(preg_match("~binary~",$n["type"])?"<code class='jush-sql'>UNHEX</code>":($n["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($n["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$n["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$N){return($N?parent::insert($Q,$N):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$J,array$Uj){$e=array_keys(reset($J));$Pj="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$w)$Y[$w]="$w = VALUES($w)";$gm="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$x=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&(strlen($Pj)+$x+strlen($X)+strlen($gm)>1e6)){if(!queries($Pj.implode(",\n",$Y).$gm))return
false;$Y=array();$x=0;}$Y[]=$X;$x+=strlen($X)+2;}return
queries($Pj.implode(",\n",$Y).$gm);}function
slowQuery($F,$Pm){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Pm FOR $F";elseif(preg_match('~^(SELECT\b)(.+)~is',$F,$_))return"$_[1] /*+ MAX_EXECUTION_TIME(".($Pm*1000).") */ $_[2]";}}function
convertColumn($t,array$n){if(preg_match("~binary~",$n["type"]))return"HEX($t)";if($n["type"]=="bit")return"BIN($t + 0)";if($n["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$n["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$W,array$n){return($this->convertColumn($t,$n)?:(preg_match('~'.text_type().'~',$n["type"])&&!preg_match("~^utf8~",$n["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$n){$A=parent::typeName($n);if($A!=""){$un=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($un,$A,strtolower($A));}$un=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$H=idx($un,$n->type,"");return($n->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$H):$H);}function
quoteBinary($Uk){return"X".q(bin2hex($Uk));}function
md5($d,array$n){if(is_blob($n)||preg_match('~'.text_type().'~',$n["type"]))return"MD5(".(is_blob($n)||preg_match("~^utf8~",$n["collation"])?$d:"CONVERT($d USING ".charset($this->conn).")").")";}function
warnings(){$G=$this->conn->query("SHOW WARNINGS");if($G&&$G->num_rows){ob_start();print_select_result($G);return
ob_get_clean();}}function
errorLine($F){return(preg_match('~ at line (\d+)$~',$this->conn->error,$_)?(int)$_[1]:0);}function
tableHelp($A,$fg=false){$Rg=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Rg?"$A-table/":str_replace("_","-",$A)."-table.html"));if(DB=="sys")return($Rg?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$A)).".html"));if(DB=="mysql")return($Rg?"mysql$A-table/":"system-schema.html");}function
partitionsInfo($Q){$_e="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$G=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $_e ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$I=($G?$G->fetch_row():null);if(!$I)return
array();$H=array();list($H["partition_by"],$H["partition"],$H["partitions"])=$I;$rj=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $_e AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$H["partition_names"]=array_keys($rj);$H["partition_values"]=array_values($rj);return$H;}function
checkConstraints($Q){$H=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$H:array_map('stripslashes',$H));}function
hasCStyleEscapes(){static$hb;if($hb===null){$Sl=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$hb=(strpos($Sl,'NO_BACKSLASH_ESCAPES')===false);}return$hb;}function
hasEstimatedRows(){return
true;}function
isSystem($k,$K=""){return
information_schema($k,$K)||in_array($k,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$H=array();foreach(get_rows("SHOW ENGINES")as$I){if(preg_match("~YES|DEFAULT~",$I["Support"]))$H[]=$I["Engine"];}return$H;}function
indexAlgorithms(array$om){return(preg_match('~^(MEMORY|NDB)$~',$om["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($pe){$H=get_session("dbs");if($H===null){$F="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Wl=microtime(true);$H=($pe?slow_query($F):get_vals($F));if(microtime(true)-$Wl>0.1){restart_session();set_session("dbs",$H);stop_session();}}return$H;}function
limit($F,$Z,$y,$B=0,$ll=" "){return" $F$Z".($y?$ll."LIMIT $y".($B?" OFFSET $B":""):"");}function
limit1($Q,$F,$Z,$ll="\n"){return
limit($F,$Z,1,0,$ll);}function
db_collation($k,array$Gb){$H=null;$i=get_val("SHOW CREATE DATABASE ".idf_escape($k),1);if(preg_match('~ COLLATE ([^ ]+)~',$i,$_))$H=$_[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$i,$_))$H=$Gb[$_[1]][-1];return$H;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$j){$H=array();foreach($j
as$k)$H[$k]=count(get_vals("SHOW TABLES IN ".idf_escape($k)));return$H;}function
table_status($A="",$Wd=false){$H=array();$F="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($A!=""?"AND TABLE_NAME = ".q($A):"ORDER BY Name");$K=array();foreach(($Wd?array():get_rows($F))as$I)$K[$I["Name"]]=$I;$Tj=null;foreach(get_rows($Wd?$F:"SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):""))as$I){$Vi=idx($K,$I["Name"]);if($Vi){if($I["Comment"]!==$Vi["Comment"]&&$I["Comment"]!==$Tj)$I["Error"]=$I["Comment"];$Tj=$I["Comment"];$I["Comment"]=$Vi["Comment"];$I["Engine"]=$Vi["Engine"];}if($I["Engine"]=="InnoDB")$I["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$I["Comment"]);if(!isset($I["Engine"]))$I["Comment"]="";if($A!="")$I["Name"]=$A;$H[$I["Name"]]=$I;}return$H;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($Ce){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Ce,$_);return
array($_[1],$_[2],ltrim($_[3].$_[4]));}function
fields($Q){$Rg=(connection()->flavor=='maria');$H=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$I){$n=$I["COLUMN_NAME"];$T=$I["COLUMN_TYPE"];$Ie=$I["GENERATION_EXPRESSION"];$Td=$I["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Td,$He);list($tn,$x,$Dn)=parse_type($T);$l=$I["COLUMN_DEFAULT"];$Bc=false;if($l!=""){$lk=$Rg;if($Rg)$Bc=!preg_match("~^('(?:[^'\\\\]|\\\\.|'')*'|NULL|[-+]?[\\d.]+)$~s",$l);elseif(preg_match('~text|json~',$tn)||preg_match('~\bDEFAULT_GENERATED\b~',$Td)){$l=stripslashes($l);$lk=preg_match("~^(_\\w+)?('(?:[^'\\\\]|\\\\.|'')*')$~s",$l,$_);if($lk)$l=$_[2];else$Bc=true;}elseif(preg_match("~^[bx]'[\\da-f]*'$~i",$l))$Bc=true;if($lk)$l=($l=="NULL"?null:(preg_match("~^'.*'$~s",$l)?stripslashes(idf_unescape($l)):$l));if(preg_match('~binary~',$tn)&&preg_match("~^(?:0x(\\w*)|x'(\\w*)')$~",$l,$_)){$l=pack("H*",$_[1].idx($_,2));$Bc=false;}}$H[$n]=array("field"=>$n,"full_type"=>$T,"type"=>$tn,"length"=>$x,"unsigned"=>$Dn,"default"=>($He?($Rg?$Ie:stripslashes($Ie)):$l),"default_sql"=>(!$He&&$Bc),"null"=>($I["IS_NULLABLE"]=="YES"),"auto_increment"=>($Td=="auto_increment"),"on_update"=>(preg_match('~\bon update current_timestamp\b~i',$Td)?"CURRENT_TIMESTAMP":""),"collation"=>$I["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$I[PRIVILEGES],where,order")),"comment"=>$I["COLUMN_COMMENT"],"primary"=>($I["COLUMN_KEY"]=="PRI"),"generated"=>($He[1]=="PERSISTENT"?"STORED":$He[1]),);}return$H;}function
indexes($Q,$h=null){$H=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$h)as$I){$A=$I["Key_name"];$H[$A]["type"]=($A=="PRIMARY"?"PRIMARY":($I["Index_type"]=="FULLTEXT"?"FULLTEXT":($I["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$I["Index_type"])?$I["Index_type"]:"INDEX"):"UNIQUE")));$H[$A]["columns"][]=$I["Column_name"];$H[$A]["lengths"][]=($I["Index_type"]=="SPATIAL"?null:$I["Sub_part"]);$H[$A]["descs"][]=null;$H[$A]["algorithm"]=$I["Index_type"];}return$H;}function
foreign_keys($Q){static$zj='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$H=array();$ic=get_val("SHOW CREATE TABLE ".table($Q),1);if($ic){preg_match_all("~CONSTRAINT ($zj) FOREIGN KEY ?\\(((?:$zj,? ?)+)\\) REFERENCES ($zj)(?:\\.($zj))? \\(((?:$zj,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$ic,$Ug,PREG_SET_ORDER);foreach($Ug
as$_){preg_match_all("~$zj~",$_[2],$Ll);preg_match_all("~$zj~",$_[5],$Fm);$H[idf_unescape($_[1])]=array("db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('Adminer\idf_unescape',$Ll[0]),"target"=>array_map('Adminer\idf_unescape',$Fm[0]),"on_delete"=>($_[6]?:"RESTRICT"),"on_update"=>($_[7]?:"RESTRICT"),);}}return$H;}function
view($A){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($A),1)));}function
collations(){$H=array();foreach(get_rows("SHOW COLLATION")as$I){if($I["Default"])$H[$I["Charset"]][-1]=$I["Collation"];else$H[$I["Charset"]][]=$I["Collation"];}ksort($H);foreach($H
as$w=>$W)sort($H[$w]);return$H;}function
information_schema($k,$K=""){return($k=="information_schema")||(min_version(5.5)&&$k=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($k,$Fb){return
queries("CREATE DATABASE ".idf_escape($k).($Fb?" COLLATE ".q($Fb):""));}function
drop_databases(array$j){$H=apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$H;}function
rename_database($A,$Fb){$H=false;if(create_database($A,$Fb)){$S=array();$do=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$do[]=$Q;else$S[]=$Q;}$H=(!$S&&!$do)||move_tables($S,$do,$A);drop_databases($H?array(DB):array());}return$H;}function
auto_increment(){$Oa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$Oa="";break;}if($u["type"]=="PRIMARY")$Oa=" UNIQUE";}}return" AUTO_INCREMENT$Oa";}function
alter_table($Q,$A,array$o,array$re,$f,$vd,$Fb,$Na,$qj){$b=array();foreach($o
as$n){if($n[1]){$l=$n[1][3];if(preg_match('~ GENERATED~',$l)){$n[1][3]=(connection()->flavor=='maria'?"":$n[1][2]);$n[1][2]=$l;}$b[]=($Q!=""?($n[0]!=""?"CHANGE ".idf_escape($n[0]):"ADD"):" ")." ".implode($n[1]).($Q!=""?$n[2]:"");}else$b[]="DROP ".idf_escape($n[0]);}$b=array_merge($b,$re);$O=($f!==null?" COMMENT=".q($f):"").($vd?" ENGINE=".q($vd):"").($Fb?" COLLATE ".q($Fb):"").($Na!=""?" AUTO_INCREMENT=$Na":"");if($qj){$rj=array();if($qj["partition_by"]=='RANGE'||$qj["partition_by"]=='LIST'){foreach($qj["partition_names"]as$w=>$W){$X=$qj["partition_values"][$w];$rj[]="\n  PARTITION ".idf_escape($W)." VALUES ".($qj["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $qj[partition_by]($qj[partition])";if($rj)$O
.=" (".implode(",",$rj)."\n)";elseif($qj["partitions"])$O
.=" PARTITIONS ".(+$qj["partitions"]);}elseif($qj===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$A)$b[]="RENAME TO ".table($A);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$nb=array();foreach($b
as$W)$nb[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$nb));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$do){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$do)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$do,$Fm){$_k=array();foreach($S
as$Q)$_k[]=table($Q)." TO ".idf_escape($Fm).".".table($Q);if(!$_k||queries("RENAME TABLE ".implode(", ",$_k))){$Fc=array();foreach($do
as$Q)$Fc[table($Q)]=view($Q);connection()->select_db($Fm);$k=idf_escape(DB);foreach($Fc
as$A=>$co){if(!queries("CREATE VIEW $A AS ".str_replace(" $k."," ",$co["select"]))||!queries("DROP VIEW $k.$A"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$do,$Fm){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$A=($Fm==DB?table("copy_$Q"):idf_escape($Fm).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $A"))||!queries("CREATE TABLE $A LIKE ".table($Q))||!queries("INSERT INTO $A SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){$jn=$I["Trigger"];list($Fd,$ki)=trigger_event($I);if(!queries("CREATE TRIGGER ".($Fm==DB?idf_escape("copy_$jn"):idf_escape($Fm).".".idf_escape($jn))." $I[Timing] $Fd".($ki!=""?" $ki":"")." ON $A FOR EACH ROW\n$I[Statement];"))return
false;}}foreach($do
as$Q){$A=($Fm==DB?table("copy_$Q"):idf_escape($Fm).".".table($Q));$co=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $A"))||!queries("CREATE VIEW $A AS $co[select]"))return
false;}return
true;}function
trigger_event(array$I){$Hd=explode(",",$I["Event"]);$H=array();foreach(array("DELETE","INSERT","UPDATE")as$Fd){if(in_array($Fd,$Hd))$H[]=$Fd;}$H=implode(" OR ",$H);if(in_array("UPDATE",$Hd)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($I["Trigger"]),2),$_)&&preg_match('~\bOF\s+(.+)~is',$_[1],$ki))return
array("$H OF",$ki[1]);return
array($H,"");}function
trigger($A,$Q){if($A=="")return
array();$J=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($A));$H=reset($J);if($H)list($H["Event"],$H["Of"])=trigger_event($H);return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){list($Fd)=trigger_event($I);$H[$I["Trigger"]]=array($I["Timing"],$Fd);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($A,$T){$J=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($A)."
ORDER BY ORDINAL_POSITION");$o=array();foreach($J
as$I){$Ce=$I["DTD_IDENTIFIER"];list($tn,$x,$Dn)=parse_type($Ce);$o[]=array("field"=>$I["PARAMETER_NAME"],"type"=>$tn,"length"=>$x,"unsigned"=>$Dn,"null"=>true,"full_type"=>$Ce,"inout"=>($T=="FUNCTION"?"":$I["PARAMETER_MODE"]),"collation"=>$I["COLLATION_NAME"],);}$H=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($A))->fetch_assoc();if(!$H)return
array();$H['options']=array("DEFINER"=>$H['definer'],"DETERMINISTIC"=>$H['is_deterministic'],"SQL_DATA_ACCESS"=>$H['data_access'],"SQL_SECURITY"=>$H['security'],"COMMENT"=>$H['comment'],);if($o&&$o[0]['field']=='')$H['returns']=array_shift($o);$H['fields']=$o;return$H;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($Lk){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($A,array$I){return
idf_escape($A);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$g,$F){return$g->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$F);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$Na,$em){$H=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$Na)$H=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$H);return$H;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($uc,$em=""){$A=idf_escape($uc);$H="";if(preg_match('~CREATE~',$em)&&($i=get_val("SHOW CREATE DATABASE $A",1))){set_utf8mb4($i);if($em=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $A;\n";$H
.="$i;\n";}return$H."USE $A";}function
trigger_sql($Q){$H="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$I){list($I["Event"],$I["Of"])=trigger_event($I);$H
.="\n".create_trigger(" ON ".table($I["Table"]),$I+array("Type"=>"FOR EACH ROW")).";\n";}return$H;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$n){return
driver()->convertColumn(idf_escape($n["field"]),$n);}function
unconvert_field(array$n,$H){if(preg_match("~binary~",$n["type"]))$H="UNHEX($H)";if($n["type"]=="bit")$H="CONVERT(b$H, UNSIGNED)";if($n["type"]=="vector")$H=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($H)";if(preg_match("~geom|point|linestring|polygon~",$n["type"])){$Pj=(min_version(8)?"ST_":"");$H=$Pj."GeomFromText($H, $Pj"."SRID($n[field]))";}return$H;}function
support($Xd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Xd);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Sd=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($K,$h=null){return
true;}function
schema_comment($K){return"";}function
set_schema_comment($K,$f){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Rm,$m="",$db=array(),$Sm="",$di=false,$Xc=""){if($di){header("HTTP/1.1 404 Not Found");$m=($m?:lang(97));}page_headers();if(is_ajax()&&$m){page_messages($m);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Tm=$Rm.($Sm!=""?": $Sm":"");$Um=strip_tags($Tm.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(98),'\' class=\'',lang(98),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Um,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.2.0+584b178"),'">
';$mc=adminer()->css();if(is_int(key($mc)))$mc=array_fill_keys($mc,'light');$Ye=in_array('light',$mc)||in_array('',$mc);$We=in_array('dark',$mc)||in_array('',$mc);$qc=($Ye?($We?null:false):($We?:null));$jh=" media='(prefers-color-scheme: dark)'";if($qc!==false)echo"<link rel='stylesheet'".($qc?"":$jh)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.2.0+584b178")."'>\n";echo"<meta name='color-scheme' content='".($qc===null?"light dark":($qc?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.2.0+584b178");if(adminer()->head($qc))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+584b178")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($mc
as$Kn=>$zh){$c=($zh=='dark'&&!$qc?$jh:($zh=='light'&&$We?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($Kn)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(99))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(100)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($db!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$M=adminer()->serverName(SERVER);$M=($M!=""?$M:lang(40));if($db===false)echo"$M\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$M</a> » ";$cl="";if(is_string($db)){$cl=$db;$db=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($db))){$wc="$z&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($wc.($_GET["ns"]==""?$cl:"")).'">'.h(DB).'</a> » ';}if(is_array($db)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$cl).'">'.h($_GET["ns"]).'</a> » ';foreach($db
as$w=>$W){$Ic=(is_array($W)?$W[1]:h($W));if($Ic!="")echo"<a href='".h(ME."$w=").url_escape(is_array($W)?$W[0]:$W)."'>$Ic</a> » ";}}echo"$Rm\n";}}echo"<h2>$Tm$Xc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($m);adminer()->serviceWorker();$j=&get_session("dbs");if(DB!=""&&$j&&!in_array(DB,$j,true))$j=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($di){page_footer($di===true?"":$di);exit;}}function
service_worker(){$xk=has_passwords();$Cb=($xk?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.2.0+584b178")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$Cb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$yl){foreach($yl
as$Tn){foreach($Tn
as$D){if($D!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$lc){$df=array();foreach($lc
as$w=>$W)$df[]="$w $W";header("Content-Security-Policy: ".implode("; ",$df));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Qn=array();foreach(array_keys(adminer()->css())as$Kn)$Qn[preg_replace('~\?.*~','',$Kn)]=true;$H=array();foreach(array("adminer.css","adminer-dark.css")as$p){if($Qn[$p]&&file_exists($p)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($p),$_);$H[$p]=array((string)$_[1],Plugins::checksum($p));}}return$H;}function
official_design_checksums(){return
array('adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'1b8ac749','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$ci;if(!$ci)$ci=base64_encode(rand_string());return$ci;}function
page_messages($m){$Jn=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$qh=idx($_SESSION["messages"],$Jn);if($qh){echo"<div class='message'>".implode("</div>\n<div class='message'>",$qh)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Jn]);}if($m)echo"<div class='error'>$m</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($yh=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($yh);echo"</div>\n";if($yh!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(42),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(101),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Hh){while($Hh>=2147483648)$Hh-=4294967296;while($Hh<=-2147483649)$Hh+=4294967296;return(int)$Hh;}function
long2str(array$V,$fo){$Uk='';foreach($V
as$W)$Uk
.=pack('V',$W);if($fo)return
substr($Uk,0,end($V));return$Uk;}function
str2long($Uk,$fo){$V=array_values(unpack('V*',str_pad($Uk,4*ceil(strlen($Uk)/4),"\0")));if($fo)$V[]=strlen($Uk);return$V;}function
xxtea_mx($ro,$qo,$hm,$kg){return
int32((($ro>>5&0x7FFFFFF)^$qo<<2)+(($qo>>3&0x1FFFFFFF)^$ro<<4))^int32(($hm^$qo)+($kg^$ro));}function
encrypt_string($bm,$w){if($bm=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$V=str2long($bm,true);$Hh=count($V)-1;$ro=$V[$Hh];$qo=$V[0];$fk=floor(6+52/($Hh+1));$hm=0;while($fk-->0){$hm=int32($hm+0x9E3779B9);$id=$hm>>2&3;for($bj=0;$bj<$Hh;$bj++){$qo=$V[$bj+1];$Gh=xxtea_mx($ro,$qo,$hm,$w[$bj&3^$id]);$ro=int32($V[$bj]+$Gh);$V[$bj]=$ro;}$qo=$V[0];$Gh=xxtea_mx($ro,$qo,$hm,$w[$bj&3^$id]);$ro=int32($V[$Hh]+$Gh);$V[$Hh]=$ro;}return
long2str($V,false);}function
decrypt_string($bm,$w){if($bm=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$V=str2long($bm,false);$Hh=count($V)-1;$ro=$V[$Hh];$qo=$V[0];$fk=floor(6+52/($Hh+1));$hm=int32($fk*0x9E3779B9);while($hm){$id=$hm>>2&3;for($bj=$Hh;$bj>0;$bj--){$ro=$V[$bj-1];$Gh=xxtea_mx($ro,$qo,$hm,$w[$bj&3^$id]);$qo=int32($V[$bj]-$Gh);$V[$bj]=$qo;}$ro=$V[$Hh];$Gh=xxtea_mx($ro,$qo,$hm,$w[$bj&3^$id]);$qo=int32($V[0]-$Gh);$V[0]=$qo;$hm=int32($hm-0x9E3779B9);}return
long2str($V,true);}$Bj=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($w)=explode(":",$W);$Bj[$w]=$W;}}function
add_invalid_login(){$Va=get_temp_dir()."/adminer-invalid";foreach(glob("$Va*")?:array($Va)as$p){$xe=file_open_lock($p);if($xe)break;}if(!$xe)$xe=file_open_lock("$Va-".rand_string());if(!$xe)return;$Xf=json_decode(stream_get_contents($xe),true);$Om=time();if($Xf){foreach($Xf
as$Yf=>$W){if($W[0]<$Om)unset($Xf[$Yf]);}}$Vf=&$Xf[adminer()->bruteForceKey()];if(!$Vf)$Vf=array($Om+30*60,0);$Vf[1]++;file_write_unlock($xe,json_encode($Xf));}function
check_invalid_login(array&$Bj){$Xf=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$p){$xe=file_open_lock($p);if($xe){$Xf=json_decode(stream_get_contents($xe),true);file_unlock($xe);break;}}$w=adminer()->bruteForceKey();$Vf=idx($Xf,$w,array());$bi=($Vf[1]>29?$Vf[0]-time():0);if($bi>0){$m=lang(102,ceil($bi/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$m
.='<br>'.lang(103,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($m,$Bj,false);}}function
password_required(){static$H;if($H===null){$H=(bool)get_session("password_required");if(!$H){$kc=adminer()->credentials();$H=!is_object(Driver::connect($kc[0],$kc[1],""));if($H)set_session("password_required",true);}}return$H;}function
require_password_link($D){$Ch="<a href='https://www.adminer.org/password/'".target_blank().">".lang(104)."</a>";if(!function_exists('password_hash'))return" $Ch";$Ej=($D!==null?$D:base64_encode(substr(pack("H*",rand_string()),0,12)));$cf=password_hash($Ej,PASSWORD_DEFAULT);$p="adminer-plugins.php";$Md=file_exists("adminer-plugins.php");if($Md)$Tf=($D!==null?lang(105,"<b>$p</b>"):lang(106,"<b>$p</b>","<b>$Ej</b>"));else{$p="<button name='password_less' value='".h($cf)."' class='link'>$p</button>";$Tf=($D!==null?lang(107,$p):lang(108,$p,"<b>$Ej</b>"));}$Ig="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($cf)."'</span>),";$H="<p>$Tf
<pre><code class='jush'>".($Md?$Ig:"&lt;?php\n<a>return</a> <a>array</a>(\n$Ig\n);")."</code></pre>
<p>$Ch
";return" <a href='#password-less' class='toggle'>".lang(109)."</a>
<div id='password-less' class='hidden'>".($Md?$H:"<form action='' method='post'>\n".$H.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ma=$_POST["auth"];if($Ma&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$ao=$Ma["driver"];$M=$Ma["server"];$U=$Ma["username"];$D=(string)$Ma["password"];$k=$Ma["db"];set_password($ao,$M,$U,$D);$_SESSION["db"][$ao][$M][$U][$k]=true;if($Ma["permanent"]){$w=implode("-",array_map('base64_encode',array($ao,$M,$U,$k)));$Xj=adminer()->permanentLogin(true);$Bj[$w]="$w:".base64_encode($Xj?encrypt_string($D,$Xj):"");cookie("adminer_permanent",implode(" ",$Bj));}if(!array_diff(array_keys($_POST),array("auth","token"))||$ao!=DRIVER||$M!=SERVER||$U!==$_GET["username"]||$k!=DB)redirect(auth_url($ao,$M,$U,$k));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($Bj);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(110).' '.str_replace('<a ','<a'.target_blank().' ',lang(111)));}elseif($Bj&&!$_SESSION["pwds"]){session_regenerate_id();$Xj=adminer()->permanentLogin();foreach($Bj
as$w=>$W){list(,$zb)=explode(":",$W);list($ao,$M,$U,$k)=array_map('base64_decode',explode("-",$w));set_password($ao,$M,$U,decrypt_string(base64_decode($zb),$Xj));$_SESSION["db"][$ao][$M][$U][$k]=true;}}function
unset_permanent(array&$Bj){foreach($Bj
as$w=>$W){list($ao,$M,$U,$k)=array_map('base64_decode',explode("-",$w));if($ao==DRIVER&&$M==SERVER&&$U==$_GET["username"]&&$k==DB)unset($Bj[$w]);}cookie("adminer_permanent",implode(" ",$Bj));}function
auth_error($m,array&$Bj,$Wf=true){$zl=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$zl]||$_GET[$zl])&&!$_SESSION["token"])$m=lang(112);elseif($Wf&&($D=get_password())!==null){restart_session();add_invalid_login();if($D===false)$m
.=($m?'<br>':'').lang(113,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Bj);}}if(!$_COOKIE[$zl]&&$_GET[$zl]&&ini_bool("session.use_only_cookies"))$m=lang(114);$gj=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$gj["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(45),$m,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(115)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Bj);page_header(lang(116),lang(117,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$g='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Bj);$kc=adminer()->credentials();$g=Driver::connect($kc[0],$kc[1],$kc[2]);if(is_object($g)){Db::$instance=$g;Driver::$instance=new
Driver($g);if($g->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Pg=null;if(!is_object($g)||($Pg=adminer()->login($_GET["username"],get_password()))!==true){$m=(is_string($g)?nl_br(h($g)):(is_string($Pg)?$Pg:lang(118))).(preg_match('~^ | $~',get_password())?'<br>'.lang(119):'');auth_error($m,$Bj);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(101),lang(120));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Ma&&$_POST["token"])$_POST["token"]=get_token();$m='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$m=lang(120).' '.lang(121);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$m=lang(122,"<b>post_max_size</b>");if(isset($_GET["sql"]))$m
.=' '.lang(123);}function
print_select_result($G,$h=null,array$Oi=array(),&$y=0,&$jd=false){$Kg=array();$v=array();$e=array();$S=array();$Uj=array();$nd=array();$un=array();$H=array();$_h=$jd;$jd=false;for($r=0;(!$y||$r<$y)&&is_array($I=$G->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($_h?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($hg=0;$hg<count($I);$hg++){$n=$G->fetch_field();$A=$n->name;$Q=(isset($n->table)?$n->table:"");$Ni=(isset($n->orgtable)?$n->orgtable:"");$Mi=(isset($n->orgname)?$n->orgname:$A);$tn=driver()->typeName($n);if($Oi&&JUSH=="sql")$Kg[$hg]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($Ni!=""){$wa=($Q!=""?$Q:$Ni);if($Q!="")$H[$Q]=$Ni;if(!isset($v[$wa])){if(!isset($Uj[$Ni])){$Uj[$Ni]=array();foreach(indexes($Ni,$h)as$u){if($u["type"]=="PRIMARY"){$Uj[$Ni]=array_flip($u["columns"]);break;}}}$S[$wa]=$Ni;$v[$wa]=$Uj[$Ni];$e[$wa]=$Uj[$Ni];}if(isset($e[$wa][$Mi])){unset($e[$wa][$Mi]);$v[$wa][$Mi]=$hg;$Kg[$hg]=$wa;}elseif($_h&&isset($n->orgname)&&$n->db==DB&&!is_blob(array("type"=>$tn)))$nd[$hg]=array($wa,$Mi,preg_match('~text|json|lob~',$tn));}$un[$hg]=$tn;echo"<th title='".h(trim(($Ni!=""?"$Ni.$Mi":($n->name!=$Mi?$Mi:""))." ".$tn))."'>".h($A).($Oi?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($A),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}foreach($nd
as$hg=>$kb){if($e[$kb[0]])unset($nd[$hg]);}echo"<tbody>\n";}$vf=array();foreach($v
as$wa=>$u){if($u&&!$e[$wa]){$t="";foreach($u
as$Db=>$hg){if($I[$hg]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($Db))."]=".url_escape($I[$hg]);}$vf[$wa]=$t;}}echo"<tr>";foreach($I
as$w=>$W){$z="";if(isset($Kg[$w])){if($Oi&&JUSH=="sql"){$Q=$I[array_search("table=",$Kg)];$z=ME.$Kg[$w].url_escape($Oi[$Q]!=""?$Oi[$Q]:$Q);}elseif(idx($vf,$Kg[$w])!==null)$z=ME."edit=".url_escape($S[$Kg[$w]]).$vf[$Kg[$w]];}$qf=select_value($W,$z,array('type'=>(preg_match('~binary~',$un[$w])?'blob':$un[$w])),null);$c="";$kb=idx($nd,$w);if($kb&&idx($vf,$kb[0])!==null&&is_utf8($W)){$jd=true;$c=" data-name='".h("val[".bracket_escape($S[$kb[0]])."][".bracket_escape(substr($vf[$kb[0]],1))."][".bracket_escape($kb[1])."]")."' data-text='".($kb[2]?1:0)."'".(html_entity_decode(strip_tags($qf),ENT_QUOTES,"UTF-8")!==$W?" data-value='".h($W)."'":"");}echo"<td".(preg_match(number_type(),$un[$w])?" class='number'":"")."$c>$qf";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(16))."\n";return$H;}function
textarea($A,$X,$J=10,$Hb=80,$jg=JUSH,$Ad=0){echo"<textarea name='".h($A)."' rows='$J' cols='$Hb' class='sqlarea jush-".h($jg)."' spellcheck='false' wrap='off'".($Ad?" data-error-line='$Ad'":"").">\n";if(is_array($X)){foreach($X
as$W)echo
h($W[0])."\n\n\n";}else
echo
h($X);echo"</textarea>";}function
select_input($c,array$Hi,$X="",$Cj=""){if($Hi&&$X!=""&&!isset($Hi[$X]))$Hi=array($X=>$X)+$Hi;$Em=($Hi?"select":"input");return"<$Em$c".($Hi?"><option value=''>$Cj".optionlist($Hi,$X,true)."</select>":" size='10' value='".h($X)."' placeholder='$Cj'>");}function
json_row($w,$W=null,$Ed=true){static$je=true;if($je)echo"{";if($w!=""){echo($je?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($W!==null?($Ed?'"'.addcslashes($W,"\r\n\"\\/").'"':$W):'null');$je=false;}else{echo"\n}\n";$je=true;}}function
flat_collations(){$Gb=collations();return(is_array(reset($Gb))?call_user_func_array('array_merge',array_values($Gb)):$Gb);}function
edit_type($w,array$n,array$Gb,array$te=array(),array$Ud=array()){$T=(string)$n["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($T&&!array_key_exists($T,driver()->types())&&!isset($te[$T])&&!in_array($T,$Ud))$Ud[]=$T;$cm=driver()->structuredTypes();if($te)$cm[lang(124)]=$te;echo
optionlist(array_merge($Ud,$cm),$T),"</select><td>","<input name='".h($w)."[length]' value='".h($n["length"])."' size='3'".(!$n["length"]&&preg_match('~var(char|binary)$~',$T)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($Gb?"<input list='collations' name='".h($w)."[collation]'".option_types($T,'('.text_type().')$')." value='".h($n["collation"])."' placeholder='(".lang(125).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($T,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$n["unsigned"]).'</select>':''),(isset($n['on_update'])?"<select name='".h($w)."[on_update]'".option_types($T,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(126).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?"CURRENT_TIMESTAMP":$n["on_update"])).'</select>':''),($te?"<select name='".h($w)."[on_delete]'".option_types($T,'`')."><option value=''>(".lang(127).")".optionlist(explode("|",driver()->onActions),$n["on_delete"])."</select> ":" ");}function
option_types($T,$un){return" data-types='".h($un)."'".(preg_match("~$un~",$T)?"":" class='hidden'");}function
process_length($x){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$x))return"(max)";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*(\d+)\s+(CHAR|BYTE)\s*\)?\s*$~i',$x,$_))return"($_[1] ".strtoupper($_[2]).")";$yd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$yd(?:\\s*,\\s*$yd)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$yd~",$x,$Ug)?"(".implode(",",$Ug[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($W){$W=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$W);$lk="'(?:[^']|'')*+'";$gg=(preg_match("~^\\s*$lk(?:\\s*,\\s*$lk)*+\\s*\$~",$W)&&preg_match_all("~$lk~",$W,$Ug)?array_map('Adminer\idf_unescape',$Ug[0]):array_map('trim',explode(",",$W)));return"(".implode(", ",array_map('Adminer\q',$gg)).")";}function
process_between($W){$lk="('(?:[^']|'')*+')";$nk=(preg_match("~^\\s*$lk\\s+AND\\s+$lk\\s*\$~i",$W,$_)?array(idf_unescape($_[1]),idf_unescape($_[2])):preg_split('~\s+AND\s+~i',trim($W)));return(count($nk)==2?q($nk[0])." AND ".q($nk[1]):"NULL AND NULL");}function
process_type(array$n,$Eb="COLLATE"){return" ".(is_user_type($n["type"])?idf_escape($n["type"]):$n["type"]).process_length($n["length"]).(preg_match(number_type(),$n["type"])&&in_array($n["unsigned"],driver()->unsigned)?" $n[unsigned]":"").(preg_match('~'.text_type().'~',$n["type"])&&$n["collation"]?" $Eb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$n["collation"])?$n["collation"]:q($n["collation"])):"");}function
process_field(array$n,array$qn){return
array(idf_escape(trim($n["field"])),process_type($qn),($n["null"]?" NULL":" NOT NULL"),default_value($n),(preg_match('~timestamp|datetime~',$n["type"])&&$n["on_update"]?" ON UPDATE $n[on_update]".process_length($n["length"]):""),(support("comment")&&$n["comment"]!=""?" COMMENT ".q($n["comment"]):""),($n["auto_increment"]?auto_increment():null),);}function
default_value(array$n){if($n["default"]===null)return"";$l=str_replace("\r","",$n["default"]);$He=$n["generated"];$P=!preg_match('~]$~',$n["length"])&&(preg_match('~char|binary|text|clob|json|enum|set|String~',$n["type"])||driver()->enumLength($n));$hj=(JUSH=="sqlite"||(JUSH=="sql"&&$n["default_sql"]&&!preg_match("~^(current_timestamp(\\(\\d*\\))?|[bx]'[\\da-f]*')$~i",$l)));return(in_array($He,driver()->generated)?(JUSH=="mssql"?" AS ($l)".($He=="VIRTUAL"?"":" $He"):" GENERATED ALWAYS AS ($l) $He"):(preg_match('~^GENERATED ~i',$l)?" $l":" DEFAULT ".(!$n["default_sql"]&&($P||preg_match('~^(?![a-z])~i',$l))?(JUSH=="sql"&&preg_match('~text|json~',$n["type"])?"(".q($l).")":q($l)):($hj?"($l)":$l))));}function
edit_fields(array$o,array$Gb,$T="TABLE",array$te=array()){$o=array_values($o);$Ac=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$Mb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($T=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($T=="TABLE"?lang(128):lang(129)),"<th id='label-type'>".lang(58)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(130),"<th>".lang(131);if($T=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(60)."'>A_I</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'cockroach'=>"serial",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<th id='label-default'$Ac>".lang(61),(support("comment")?"<th id='label-comment'$Mb>".lang(59):"");$yg=!support("move_col");echo"<td>".icon("plus","add[".($yg?count($o):0)."]","+",lang(132),($yg?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($o
as$r=>$n){$r++;$Pi=$n[($_POST?"orig":"field")];$Pc=(isset($_POST["add"][$r-1])||(isset($n["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$Pi=="");echo"<tr".($Pc?"":" hidden").">\n",($T=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$n["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(133))." ":"");if($Pc)echo"<input name='fields[$r][field]' value='".h($n["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$Pi);edit_type("fields[$r]",$n,$Gb,$te);if($T=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$n["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($n["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ac>".html_select("fields[$r][generated]",array_merge(array("","DEFAULT","DEFAULT SQL"),driver()->generated),$n["generated"],"","label-default")." ";$c=" name='fields[$r][default]' aria-labelledby='label-default'";$X=h($n["default"]);echo(preg_match('~\n~',$n["default"])?"<textarea$c rows='2' cols='30' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");if(support("comment")){$c=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$Mb>".adminer()->commentInput('COLUMN',$c,$n["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(132))." ":""),($Pi==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(134)):"");}}function
process_fields(array&$o){if($_POST["add"]){$o=array_values($o);array_splice($o,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($cd,$i,$ed,$Km,$gd,$Og,$ph,$nh,$oh,$ti,$Uh){if($_POST["drop"])query_redirect($cd,$Og,$ph);elseif($ti=="")query_redirect($i,$Og,$oh);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($Og,$nh,queries($cd)&&queries($i)&&driver()->commit());driver()->rollback();}elseif($ti!=$Uh){$jc=queries($i);queries_redirect($Og,$nh,$jc&&queries($cd));if($jc&&$ed)queries($ed);}else
queries_redirect($Og,$nh,queries($Km)&&queries($gd)&&queries($cd)&&queries($i));}function
create_trigger($xi,array$I){$Qm=" $I[Timing] $I[Event]".(preg_match('~ OF~',$I["Event"])?" $I[Of]":"");return"CREATE TRIGGER ".idf_escape($I["Trigger"]).(JUSH=="mssql"?$xi.$Qm:$Qm.$xi).preg_replace('~[\s;]+$~',''," $I[Type]\n$I[Statement]").";";}function
q_dollar($P){$Gc='$$';while(strpos($P.$Gc,$Gc)!=strlen($P))$Gc='$_'.substr($Gc,1);return$Gc.$P.$Gc;}function
routine_collate($Fb){static$qb=array();if($Fb&&!$qb){foreach(collations()as$pb=>$Yn){foreach((array)$Yn
as$W)$qb[$W]=$pb;}}return($qb[$Fb]?"CHARACTER SET ".q($qb[$Fb])." ":"")."COLLATE";}function
create_routine($Lk,array$I){$N=array();$o=$I["fields"];ksort($o);foreach($o
as$n){if($n["field"]!=""){$Of=(preg_match("~^(".driver()->inout.")\$~",$n["inout"])?$n["inout"]:"");$N[]="\n  ".(JUSH=="mssql"?"@$n[field]".process_type($n).($Of?" $Of":""):($Of?"$Of ":"").idf_escape($n["field"]).process_type($n,routine_collate($n["collation"])));}}$Dc="";$Hi=array();foreach(routine_options($Lk)as$w=>$Y){$X=idx($I["options"],$w,"");if($w=="DEFINER")$Dc=($X?" $w=".implode("@",array_map('Adminer\q',explode("@",$X,2))):"");elseif(!$Y){if($X!="")$Hi[]="$w ".q($X);}elseif($X!=reset($Y)&&in_array($X,$Y))$Hi[]=$X;}$wg=$I["language"];$Ec=preg_replace('~[\s;]+$~','',$I["definition"]);$Yc=(JUSH=="pgsql"||($wg&&$wg!="sql"));$fj=($N?implode(",",$N)."\n":"");return"CREATE$Dc $Lk ".table(trim($I["name"])).(JUSH=="mssql"&&$Lk=="PROCEDURE"?rtrim($fj):" ($fj)").($Lk=="FUNCTION"?"\nRETURNS".process_type($I["returns"],routine_collate($I["returns"]["collation"])):"").($wg?" LANGUAGE $wg":"").($Hi?"\n".implode(" ",$Hi):"").($Yc?" AS ".q_dollar("\n".trim($Ec)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$Ec;");}function
remove_definer($F){$Dc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($Dc).')','\1',$F);}function
object_name($T,$Q,array$e){return
str_replace(array("{table}","{columns}"),array($Q,implode("_",$e)),adminer()->namePattern($T));}function
format_foreign_key(array$q,$A=""){$k=$q["db"];$ei=$q["ns"];return($A!=""?" CONSTRAINT ".idf_escape($A):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$q["source"])).") REFERENCES ".($k!=""&&$k!=$_GET["db"]?idf_escape($k).".":"").($ei!=""&&$ei!=$_GET["ns"]?idf_escape($ei).".":"").idf_escape($q["table"])." (".implode(", ",array_map('Adminer\idf_escape',$q["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$q["on_delete"])?" ON DELETE $q[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$q["on_update"])?" ON UPDATE $q[on_update]":"").($q["deferrable"]?" $q[deferrable]":"");}function
tar_file($p,$Vm){$H=pack("a100a8a8a8a12a12",$p,644,0,0,decoct($Vm->size),decoct(time()));$wb=8*32;for($r=0;$r<strlen($H);$r++)$wb+=ord($H[$r]);$H
.=sprintf("%06o",$wb)."\0 ";echo$H,str_repeat("\0",512-strlen($H));$Vm->send();echo
str_repeat("\0",511-($Vm->size+511)%512);}function
doc_version(){$xl=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$xl,$_);return($_[1]>=18?$_[1]:"19");}$wk=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$bo=(preg_match($wk,$xl,$_)?$_[0]:"");if(JUSH=='mssql')return($bo>=15?"sql-server-ver$bo":($bo==12?"azuresqldb-current":"sql-server-2017"));return$bo;}function
doc_link(array$yj,$Lm="📖"){$bo=doc_version();$Ln=array('sql'=>"https://dev.mysql.com/doc/refman/$bo/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$bo)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$bo/",);if(connection()->flavor=='maria'){$Ln['sql']="https://mariadb.com/kb/en/";$yj['sql']=($yj['mariadb']?:str_replace(".html","/",$yj['sql']));}if(connection()->flavor=='cockroach'&&$yj['cockroach']){$Ln['pgsql']="https://docs.cockroachlabs.com/docs/v$bo/";$yj['pgsql']=$yj['cockroach'];}return($yj[JUSH]?" <a href='".h($Ln[JUSH].$yj[JUSH].(JUSH=='mssql'?"?view=$bo":""))."'".target_blank()." class='doc' title='".lang(135)."'>$Lm</a>":"");}function
db_size($k){if(!connection()->select_db($k))return"?";$H=0;foreach(table_status()as$R)$H+=$R["Data_length"]+$R["Index_length"];return
adminer()->formatSizeValue($H);}function
set_utf8mb4($i){static$N=false;if(!$N&&preg_match('~\butf8mb4~i',$i)){$N=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(44).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$j=adminer()->databases();if($j)redirect(ME."db=".url_escape($j[0]));}if($_POST["db"]&&!$m)queries_redirect(substr(ME,0,-1),lang(136),drop_databases($_POST["db"]));page_header(lang(137),$m,false);echo"<p class='links'>\n";foreach(array('database'=>lang(138),'privileges'=>lang(80),'processlist'=>lang(139),'variables'=>lang(140),'status'=>lang(141),)as$w=>$W){if(support($w))echo"<a href='".h(ME)."$w='>$W</a>\n";}echo"<p>".lang(142,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(143,"<b>".h(logged_user())."</b>")."\n";$j=adminer()->databases();if($j){$Yk=support("scheme");$Gb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(44).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(144)."</a>":"")."<th>".lang(145)."<th>".lang(146)."<th>".lang(147)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(148)."</a>"."<tbody>\n";$j=($_GET["dbsize"]?count_tables($j):array_flip($j));foreach($j
as$k=>$S){$Kk=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($k);$s=h("Db-".$k);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$k,in_array($k,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$Kk' id='$s'>".h($k)."</a>";$Fb=h(db_collation($k,$Gb));echo"<td>".(support("database")?"<a href='$Kk".($Yk?"&amp;ns=":"")."&amp;database=' title='".lang(76)."'>$Fb</a>":$Fb),"<td align='right'><a href='$Kk&amp;schema=' id='tables-".h($k)."' title='".lang(79)."'>".($_GET["dbsize"]?format_number($S):"?")."</a>","<td align='right' id='size-".h($k)."'>".($_GET["dbsize"]?db_size($k):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(149)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(150)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$qa=adminer();$Gj=($qa
instanceof
Plugins?$qa->plugins:array());$bd=($qa
instanceof
Plugins?$qa->drivers:array());$Mc=design_checksums();if($Gj||$bd||$Mc){$xb=($qa
instanceof
Plugins?$qa->checksums():array());$mi=Plugins::officialChecksums();$Gn=function($Kn){return" (<a href='$Kn'".target_blank()." class='update'>".VERSION."</a>)";};$Fj=function($de)use($xb,$mi,$Gn){return($xb[$de]&&$mi[$de]&&$xb[$de]!==$mi[$de]?$Gn("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(151)."</h3>\n<ul>\n";foreach($Gj
as$Dj){$uk=new
\ReflectionObject($Dj);$Jc=Plugins::describe($Dj);echo"<li><b>".get_class($Dj)."</b>".($Jc!=""?": $Jc":"").$Fj(basename((string)$uk->getFileName(),'.php'))."\n";}foreach($bd
as$s=>$A)echo"<li><b>".h($s)."</b>: ".h($A).$Fj(basename((string)$qa->driverFiles[$s],'.php'))."\n";if($Mc){$oi=official_design_checksums();foreach($Mc
as$p=>$Lc){list($A,$wb)=$Lc;$ni=$oi["$A/$p"];echo"<li><b>".h($p)."</b>".h($A?": $A":"").($ni&&$ni!==$wb?$Gn("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~&db=[^&]+~','\0&ns='.url_escape(get_schema()),relative_uri()));if(!set_schema($_GET["ns"]))page_header(lang(88).h(": $_GET[ns]"),adminer()->error(),true,"","ns");}}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($ac){$this->size+=strlen($ac);fwrite($this->handler,$ac);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$o=fields($a);header("Content-Type: application/octet-stream");$Y=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Y)).".".friendly_url($_GET["field"]));$L=array(idf_escape($_GET["field"]));$G=driver()->select($a,$L,array(where($_GET,$o)),$L);$I=($G?$G->fetch_row():array());echo
driver()->value($I[0],$o[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$o=fields($a);if(!$o)$m=adminer()->error();$R=table_status1($a);$A=adminer()->tableName($R);$m=$m?:h($R["Error"]);page_header(($o&&is_view($R)?$R['Engine']=='materialized view'?lang(152):lang(153):lang(154)).": ".($A!=""?$A:h($a)),$m,array(),"",!$o,($o?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$Jk=array();foreach($o
as$w=>$n)$Jk+=$n["privileges"];adminer()->selectLinks($R,(isset($Jk["insert"])||!support("table")?"":null));$f=$R["Comment"];if($f!="")echo"<p class='nowrap'>".lang(59).": ".adminer()->commentValue('TABLE',$f)."\n";if($o)adminer()->tableStructurePrint($o,$R);function
tables_links(array$S){echo"<ul>\n";foreach($S
as$I){$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($I["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($I["table"]))."'>".($I["ns"]!=$_GET["ns"]?"<b>".h($I["ns"])."</b>.":"").h($I["table"])."</a>";}echo"</ul>\n";}$Mf=driver()->inheritsFrom($a);if($Mf){echo"<h3>".lang(155)."</h3>\n";tables_links($Mf);}if(support("indexes")&&driver()->supportsIndex($R)){echo"<div>\n","<h3 id='indexes'>".lang(156)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$R);if(driver()->supportsAlterIndex($R))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(157)."</a>\n";echo"</div>\n";}if(!is_view($R)&&driver()->supportsAlterTable($R)){if(fk_support($R)){echo"<div>\n","<h3 id='foreign-keys'>".lang(124)."</h3>\n";$te=foreign_keys($a);$Aa=function_exists('Adminer\alter_table');if($te){echo"<table>\n","<thead><tr><th>".lang(158)."<th>".lang(159)."<th>".lang(127)."<th>".lang(126).($Aa?"<td class='hover'>":"")."<tbody>\n";foreach($te
as$A=>$q){echo"<tr".(!is_int($A)?" title='".h($A)."'":"").">","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$q["source"]))."</i>";$z=($q["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($q["db"]),ME):($q["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($q["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($q["table"]))."'>".($q["db"]!=""&&$q["db"]!=DB?"<b>".h($q["db"])."</b>.":"").($q["ns"]!=""&&$q["ns"]!=$_GET["ns"]?"<b>".h($q["ns"])."</b>.":"").h($q["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$q["target"]))."</i>)","<td>".h($q["on_delete"]),"<td>".h($q["on_update"]),($Aa?'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($A)).'">'.lang(160).'</a>':''),"\n";}echo"</table>\n";}echo($Aa?'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(161)."</a>\n":''),"</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(162)."</h3>\n";$sb=driver()->checkConstraints($a);if($sb){echo"<table>\n";foreach($sb
as$w=>$W)echo"<tr".($w!=$W?" title='".h($w)."'":"").">","<td><code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($W)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(160)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(163)."</a>\n","</div>\n";}}if(support(is_view($R)?"view_trigger":"trigger")&&driver()->supportsAlterTable($R)){echo"<div>\n","<h3 id='triggers'>".lang(164)."</h3>\n";$nn=triggers($a);if($nn){echo"<table>\n";foreach($nn
as$w=>$W){echo"<tr valign='top'><td>".h($W[0])."<td>".h($W[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(160)."</a>";$Lk=$W[2];if($Lk){$Nk=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($Lk["ns"]),ME).'function='.url_escape($Lk["function"]).'&name='.url_escape($Lk["name"]);echo", <a href='".h($Nk)."' title='".h($Lk["name"])."'>".lang(165)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(166)."</a>\n","</div>\n";}$Bl=driver()->shadowTables($a);if($Bl){echo"<h3 id='shadow-tables'>".lang(167)."</h3>\n";tables_links($Bl);}$Lf=driver()->inheritedTables($a);if($Lf){echo"<h3 id='partitions'>".lang(168)."</h3>\n";$lj=driver()->partitionsInfo($a);if($lj)echo"<p><code class='sql jush-".JUSH."'>BY ".h("$lj[partition_by]($lj[partition])")."</code>\n";tables_links($Lf);}}elseif(isset($_GET["schema"])){page_header(lang(79),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($Q,array$tk,array&$e){if(!isset($e[$Q])){$e[$Q]=0;foreach((array)idx($tk,$Q)as$A=>$vk){if($A!=$Q)$e[$Q]=max($e[$Q],schema_column($A,$tk,$e)+1);}}return$e[$Q];}function
type_class($T){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$W){if(preg_match("~$w|$W~",$T))return" class='$w'";}}$wm=array();$ym=array();$xm=array();$ae=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Ug,PREG_SET_ORDER);foreach($Ug
as$r=>$_){$wm[$_[1]]=array((float)$_[2],(float)$_[3]);$ym[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$K=array();$tk=array();$te=array();$za=driver()->allFields();$if=array();$zm=array();foreach(table_status('',true)as$Q=>$R){if(!is_view($R)){if(adminer()->tableName($R)!=""&&!$R["dependent"])$zm[$Q]=$R;else$if[$Q]=true;}}foreach($zm
as$Q=>$R){$E=0;$K[$Q]["fields"]=array();foreach($za[$Q]as$n){$E+=1.25;$ae[$Q][$n["field"]]=$E;$K[$Q]["fields"][$n["field"]]=$n;}foreach(adminer()->foreignKeys($Q)as$W){if($W["db"]==""&&$W["ns"]==""&&!$if[$W["table"]]){$te[$Q][]=$W;$tk[$W["table"]][$Q]=array();}}}$e=array();$Le=array();$oo=array();$Se=array();foreach(array_keys($K)as$A)schema_column($A,$tk,$e);arsort($e);foreach($e
as$A=>$d){$wh=null;foreach((array)idx($te,$A)as$W){if($W["table"]!=$A&&$K[$W["table"]])$wh=($wh===null?$e[$W["table"]]:min($wh,$e[$W["table"]]));}$e[$A]=max($d,(int)$wh-1);}$Am=array();foreach($K
as$A=>$Q){$d=$e[$A];$Le[$d][]=$A;$Nm=.75*strlen($A);foreach($Q["fields"]as$n)$Nm=max($Nm,.65*strlen($n["field"]));$Am[$A]=ceil($Nm)+1;$oo[$d]=max(idx($oo,$d,0),$Am[$A]);}foreach($te
as$A=>$Yn){foreach($Yn
as$W){$Re=$e[$A]+(idx($e,$W["table"],$e[$A])>$e[$A]?1:0);$Se[$Re]=idx($Se,$Re,0)+1;}}ksort($Le);$gf=0;$no=0;$Ib=0;$Sj=null;$sm=array();foreach($Le
as$d=>$S){if($Sj!==null){$Ib=round($Ib+$oo[$Sj]+1.7+idx($Se,$d,0)*.1,1);$Ki=array();foreach($S
as$A){$hm=0;$hc=0;$Ph=array_keys((array)idx($tk,$A));foreach((array)idx($te,$A)as$W)$Ph[]=$W["table"];foreach($Ph
as$Ih){if($K[$Ih]&&$e[$Ih]<$d){$hm+=$K[$Ih]["pos"][0];$hc++;}}$Ki[$A]=($hc?$hm/$hc:$gf);}asort($Ki);$S=array_keys($Ki);}$Ym=0;foreach($S
as$A){$E=1.25*count($K[$A]["fields"]);$K[$A]["pos"]=($wm[$A]?:array($Ym,$Ib));$sm[$A]=$K[$A]["pos"][1];$Ym+=2.5+$E;$gf=max($gf,$K[$A]["pos"][0]+2.5+$E);$no=max($no,round($K[$A]["pos"][1]+$oo[$d],1));if(!$wm[$A])$xm[]="\n\t'".js_escape($A)."': [ ".$K[$A]["pos"][0].", ".$K[$A]["pos"][1]." ]";}$Sj=$d;}$Bg=array();$Wa=array();foreach($te
as$A=>$Yn){foreach($Yn
as$W){$Gm=idx($sm,$W["table"],$sm[$A]);$Ml=$sm[$A]+$oo[$e[$A]];$Ik=($Gm-1>$Ml);$_g=($Ik?$Ml+1:min($sm[$A],$Gm)-1);$Va=idx($Wa,(string)$_g,0);$Wa[(string)$_g]=$Va+1;$_g=round($Ik?min($_g+$Va*.1,$Gm-1):$_g-$Va*.1,1);while($Bg[(string)$_g])$_g-=.0001;$K[$A]["references"][$W["table"]][(string)$_g]=array($W["source"],$W["target"]);$tk[$W["table"]][$A][(string)$_g]=$W["target"];$Bg[(string)$_g]=true;}}echo'<div id="schema" style="height: ',$gf,'em; width: ',$no,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$ym)."\n",'};
const tablePosDefault = {',implode(",",$xm)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$gf,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($K
as$A=>$Q){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$Q["pos"][0]."em; left: ".$Q["pos"][1]."em; width: ".$Am[$A]."em;'>",'<a href="'.h(ME).'table='.url_escape($A).'"><b>'.h($A)."</b></a>";foreach($Q["fields"]as$n){$W='<span'.type_class($n["type"]).' title="'.h($n["type"].($n["length"]?"($n[length])":"").($n["null"]?" NULL":'')).'">'.h($n["field"]).'</span>';echo"<br>".($n["primary"]?"<i>$W</i>":$W);}foreach((array)$Q["references"]as$Hm=>$vk){foreach($vk
as$_g=>$qk){$Ag=$_g-$Q["pos"][1];$em=($Ag>0?"left: 100%; width: calc($Ag"."em - 100%)":"left: $Ag"."em");$no=($Ag>0?"100%":(-$Ag)."em");$r=0;foreach($qk[0]as$Ll)echo"\n<div class='references' title='".h($Hm)."' id='refs$_g-".($r++)."' style='$em"."; top: ".$ae[$A][$Ll]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $no;'></div></div>";}}foreach((array)$tk[$A]as$Hm=>$vk){foreach($vk
as$_g=>$Im){$Ag=$_g-$Q["pos"][1];$r=0;foreach($Im
as$Fm)echo"\n<div class='references arrow' title='".h($Hm)."' id='refd$_g-".($r++)."' style='left: $Ag"."em; top: ".$ae[$A][$Fm]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Ag)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($K
as$A=>$Q){foreach((array)$Q["references"]as$Hm=>$vk){if($K[$Hm]){foreach($vk
as$_g=>$qk){$xh=$gf;$ch=-10;foreach($qk[0]as$w=>$Ll){$Ij=$Q["pos"][0]+$ae[$A][$Ll];$Jj=$K[$Hm]["pos"][0]+$ae[$Hm][$qk[1][$w]];$xh=min($xh,$Ij,$Jj);$ch=max($ch,$Ij,$Jj);}echo"<div class='references' id='refl$_g' style='left: $_g"."em; top: $xh"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($ch-$xh)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(169),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$m){$l=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$jm){if(support($jm))$l[$jm."s"]='';}save_settings(array_intersect_key($_POST+$l,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$l),"adminer_export");$ya=(DB==""||$_GET["ns"]==="");$S=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Qd=dump_headers((count($S)==1?key($S):DB),($ya||count($S)>1));$dg=preg_match('~sql~',$_POST["format"]);if($dg){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$em=$_POST["db_style"];$j=array(DB);if(DB==""){$j=$_POST["databases"];if(is_string($j))$j=explode("\n",rtrim(str_replace("\r","",$j),"\n"));}foreach((array)$j
as$k){adminer()->dumpDatabase($k);if(connection()->select_db($k)){if($dg&&$em)echo
use_sql($k,$em).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$K){if($K!=""){if(DB==""&&information_schema(DB,$K))continue;set_schema($K);}if($dg&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$am=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$Pd=array();$tc=array();foreach($am
as$A=>$R){if($ya||in_array($A,(array)$_POST["tables"]))$Pd[$A]=$R;if($ya||in_array($A,(array)$_POST["data"]))$tc[$A]=$R;}if($dg){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($Pd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$on=array();foreach($tc
as$A=>$R){if(!is_view($R)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($Pd[$A])))$on[]=$A;}echo
truncate_all_sql($on);}$Yi="";if($_POST["types"]){foreach(types()as$s=>$T){$Ec=type_definition($s);$ii=($Ec["kind"]=='d'?"DOMAIN":"TYPE");if($Ec["definition"])$Yi
.=($em!='DROP+CREATE'?"DROP $ii IF EXISTS ".table($T).";;\n":"")."CREATE $ii ".table($T)." $Ec[definition];\n\n";else$Yi
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$T)."\n\n";}}if($_POST["routines"]){foreach(routines()as$I){$A=$I["ROUTINE_NAME"];$Lk=$I["ROUTINE_TYPE"];$Kf=routine($I["SPECIFIC_NAME"],$Lk);if($Kf["definition"]!=""){$i=create_routine($Lk,array("name"=>$A)+$Kf);set_utf8mb4($i);$Yi
.=($em!='DROP+CREATE'?"DROP $Lk IF EXISTS ".table($A).";;\n":"")."$i;\n\n";}else$Yi
.="-- Could not export ".strtolower($Lk)." ".preg_replace('~[\r\n]+~',' ',$A)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$I){$i=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($I["Name"]),3));set_utf8mb4($i);$Yi
.=($em!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($I["Name"]).";;\n":"")."$i;;\n\n";}}echo($Yi&&JUSH=='sql'?"DELIMITER ;;\n\n$Yi"."DELIMITER ;\n\n":$Yi);}if($_POST["table_style"]||$_POST["data_style"]){$do=array();foreach($am
as$A=>$R){$Q=array_key_exists($A,$Pd);$rc=array_key_exists($A,$tc);if($Q||$rc){$Vm=null;if($Qd=="tar"){$Vm=new
TmpFile;ob_start(array($Vm,'write'),1e5);}adminer()->dumpTable($A,($Q?$_POST["table_style"]:""),(is_view($R)?2:0));if(is_view($R))$do[]=$A;elseif($rc){$o=fields($A);$L=array("*");$ec=convert_fields($o,$o);if($ec)$L[]=substr($ec,2);adminer()->dumpData($A,$_POST["data_style"],"",$L);}if($dg&&$_POST["triggers"]&&$Q&&($nn=trigger_sql($A)))echo"\nDELIMITER ;;\n$nn\nDELIMITER ;\n";if($Qd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$k/")."$A.csv",$Vm);}elseif($dg)echo"\n";}}if($dg&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($Pd
as$A=>$R){if(!is_view($R))echo
foreign_keys_sql($A);}}if($dg){foreach($do
as$co)adminer()->dumpTable($co,$_POST["table_style"],1);}if($Qd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(85),$m,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$xc=array('','USE','DROP+CREATE','CREATE');$Wk=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$xc);$_m=array('','DROP+CREATE','CREATE');$sc=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$sc[]='INSERT+UPDATE';$I=get_settings("adminer_export");if(!$I)$I=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(170)."<td>".html_radios("output",adminer()->dumpOutput(),$I["output"])."\n","<tr><th>".lang(171)."<td>".html_radios("format",adminer()->dumpFormat(),$I["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(44)."<td>".html_select('db_style',$xc,$I["db_style"]).(support("type")?checkbox("types",1,$I["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$I["routines"],lang(81)):"").(support("event")?checkbox("events",1,$I["events"],lang(83)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(88)."<td>".html_select('schema_style',$Wk,$I["schema_style"]):""),"<tr><th>".lang(146)."<td>".html_select('table_style',$_m,$I["table_style"]).checkbox("auto_increment",1,$I["auto_increment"],lang(60)).(support("trigger")?checkbox("triggers",1,$I["triggers"],lang(164)):""),"<tr><th>".lang(172)."<td>".html_select('data_style',$sc,$I["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(85),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Qj=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(173)."'".on('click','formCheck','^schemas\[').">".lang(88)."</label>","<tbody>\n";foreach(adminer()->schemas()as$K){if(!information_schema(DB,$K))echo"<tr><td>".checkbox("schemas[]",$K,true,$K,"","block")."\n";}}elseif(DB!=""){$ub=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$ub class='jsonly' title='".lang(173)."'".on('click','formCheck','^tables\[').">".lang(154)."</label>","<th style='text-align: right;'><label class='block'>".lang(172)."<input type='checkbox' id='check-data'$ub class='jsonly' title='".lang(173)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$do="";$Cm=tables_list();foreach($Cm
as$A=>$T){$Pj=preg_replace('~_.*~','',$A);$ub=($a==""||$a==(substr($a,-1)=="%"?"$Pj%":$A));$Wj="<tr><td>".checkbox("tables[]",$A,$ub,$A,"","block");if($T!==null&&!preg_match('~table~i',$T))$do
.="$Wj\n";else
echo"$Wj<td align='right'><label class='block'><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$ub)."</label>\n";$Qj[$Pj]++;}echo$do;if($Cm)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$j=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($j?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(173)."'".on('click','formCheck','^databases\[').">":"").lang(44)."</label>","<tbody>\n";if($j){foreach($j
as$k){if(!information_schema($k)){$Pj=preg_replace('~_.*~','',$k);echo"<tr><td>".checkbox("databases[]",$k,$a==""||$a=="$Pj%",$k,"","block")."\n";$Qj[$Pj]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$je=true;foreach($Qj
as$w=>$W){if($w!=""&&$W>1){echo($je?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$je=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(80));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(174)."</a>";$G=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Je=$G;if(!$G)$G=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Je?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(42)."<th>".lang(40)."<td class='hover'><tbody>\n";while($I=$G->fetch_assoc())echo'<tr><td>'.h($I["User"]),"<td>".h($I["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($I["User"]).'&host='.url_escape($I["Host"])).'">'.lang(14)."</a>\n";if(!$Je||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(14)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$m&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$m&&$_POST["val"]){$ta=0;$fm=true;$lb=array();$Tk=0;foreach($_POST["val"]as$J)$Tk+=count($J);$Ya=$Tk>1&&driver()->begin();foreach($_POST["val"]as$qm=>$J){$Q=bracket_escape($qm,true);$o=fields($Q);$rm=indexes($Q);foreach($J
as$t=>$I){parse_str(bracket_escape($t,true),$Z);$yn=array();foreach($Z["where"]as$w=>$W)$yn[bracket_escape($w,true)]=$W;if(!$o||$Z["null"]||array_diff_key($yn,$o)||!unique_array($yn,$rm)){$fm=false;break
2;}$N=array();$L=array();foreach($I
as$lg=>$W){$w=bracket_escape($lg,true);$n=idx($o,$w);if(!$n){$fm=false;break
3;}$N[idf_escape($w)]=(preg_match('~char|text~',$n["type"])||$W!=""?adminer()->processInput($n,$W):"NULL");$L[$lg]=$w;}$kk=where($Z,$o);if(!driver()->update($Q,$N," WHERE $kk",0," ")){$fm=false;break
2;}$ta+=connection()->affected_rows;$e=array();foreach($L
as$w)$e[]=idf_escape($w);$Hn=driver()->select($Q,$e,array($kk),$e);$Wh=($Hn?$Hn->fetch_row():array());$hg=0;foreach($L
as$lg=>$w){$n=$o[$w];$dm=array('type'=>(preg_match('~binary~',$n["type"])?'blob':$n["type"]));$lb["val[$qm][$t][$lg]"]=select_value(idx($Wh,$hg++),"",$dm,null);}}}if($Ya&&$fm)$fm=driver()->commit();queries_redirect(null,lang(175,$ta),$fm);if($Ya&&!$fm)driver()->rollback();page_headers();page_messages($m);foreach($lb
as$A=>$W)echo"<div data-name='".h($A)."' hidden>$W</div>\n";exit;}restart_session();$mf=&get_session("queries");$lf=&$mf[DB];if(!$m&&$_POST["clear"]){$lf=array();redirect(remove_from_uri("history"));}stop_session();$ra=get_settings("adminer_import");if($_POST&&$ra)save_settings($ra,"adminer_import");page_header((isset($_GET["import"])?lang(84):lang(73)),$m);$Jg=driver()->lineComment();$Ad=0;function
query_error_line($Lm,$B,$F,$Ig){return
substr_count(substr($Lm,0,$B),"\n")+($Ig?:substr_count(substr($F,0,strspn($F," \t\r\n")),"\n")+1);}if(!$m&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$Gc=driver()->delimiter;$xe=false;if(!isset($_GET["import"]))$F=$_POST["query"];elseif($_POST["webfile"]){$Ql=adminer()->importServerPath();$xe=@fopen((file_exists($Ql)?$Ql:"compress.zlib://$Ql.gz"),"rb");$F=($xe?fread($xe,1e6):false);}else$F=get_file("sql_file",true,$Gc);if(is_string($F)){if(($kh=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($kh,strval(2*strlen($F)+memory_get_usage()+8e6)));if($F!=""&&strlen($F)<1e6){$fk=$F.(preg_match("~$Gc\\s*\$~",$F)?"":$Gc);if(!$lf||first(end($lf))!=$fk){restart_session();$lf[]=array($fk,time());set_session("queries",$mf);stop_session();}}$Nl="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Jg)[^\n]*\n?|--\r?\n)";$B=0;$jk=0;$sd=true;$gc=false;$rh=false;$h=connect();if($h&&DB!=""){$h->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$h);}$Kb=0;$Cd=array();$ij='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Jg.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$an=microtime(true);while($F!=""){if(!$B&&preg_match("~^$Nl*+DELIMITER\\s+(\\S+)~i",$F,$_)){$Gc=preg_quote($_[1]);$F=substr($F,strlen($_[0]));$jk+=strlen($_[0]);}elseif(!$B&&JUSH=='pgsql'&&preg_match("~^($Nl*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$F,$_)){$Gc="\n\\\\\\.\r?\n";$gc=true;$B=strlen($_[0]);}elseif(!$B&&JUSH=='pgsql'&&preg_match("~^$Nl*+\\\\connect[ \t][^\n]*~",$F,$_)){$Gc="\n";$rh=true;$B=strlen($_[0]);}else{preg_match("($Gc\\s*|$ij)",$F,$_,PREG_OFFSET_CAPTURE,$B);list($ve,$E)=$_[0];if(!$ve&&$xe&&!feof($xe))$F
.=fread($xe,1e5);else{if(!$ve&&rtrim($F)=="")break;$B=$E+strlen($ve);if($ve&&!preg_match("(^$Gc)",$ve)){$ib=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($E>0&&strtolower($F[$E-1])=="e"));$zj=($ve=='/*'?'\*/':($ve=='['?']':(preg_match("~^(?:$Jg)~",$ve)?"\n":preg_quote($ve).($ib?'|\\\\.':''))));while(preg_match("($zj|\$)s",$F,$_,PREG_OFFSET_CAPTURE,$B)){$Uk=$_[0][0];if(!$Uk&&$xe&&!feof($xe))$F
.=fread($xe,1e5);else{$B=$_[0][1]+strlen($Uk);if(!$Uk||$Uk[0]!="\\")break;}}}else{$fk=substr($F,0,$E+($gc?3:0));$F=substr($F,$B);$gk=$jk;$jk+=$B;$B=0;if($gc||$rh){$Gc=driver()->delimiter;$gc=false;$rh=false;}$Cb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($fk)."</code>";if(preg_match("~^$Nl*+\$~",$fk)&&!preg_match('~/\*M?!~',$fk)){echo($_POST["only_errors"]?"":"<pre>$Cb</pre>\n");continue;}$sd=false;$Kb++;$Wj="<pre id='sql-$Kb'>$Cb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Nl*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$fk,$_)!==0){echo$Wj,"<p class='error'>".lang(176,preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO')."\n";$Cd[]=" <a href='#sql-$Kb'>$Kb</a>";if(!$Ad&&!isset($_GET["import"]))$Ad=query_error_line($_POST["query"],$gk,$fk,0);if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Wj;ob_flush();flush();}$Wl=microtime(true);if(JUSH=="pgsql"&&preg_match("~^$Nl*+\\\\connect\\s+(\"(?:[^\"]|\"\")+\"|[^\\s\";]+)\\s*;*\\s*\$~",$fk,$_)){$k=idf_unescape($_[1]);if(connection()->select_db($k)){if($h)$h->select_db($k);echo($_POST["only_errors"]?"":"<p class='message'>".lang(44).": ".h($k)." <span class='time'>(".format_time($Wl).")</span>\n");continue;}}elseif(connection()->multi_query($fk)&&$h&&preg_match("~^$Nl*+USE\\b~i",$fk))$h->query($fk);do{$G=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Wj:""),"<p class='error'>".lang(177).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Cd[]=" <a href='#sql-$Kb'>$Kb</a>";if(!$Ad&&!isset($_GET["import"]))$Ad=query_error_line($_POST["query"],$gk,$fk,driver()->errorLine($fk));if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($fk));$Om=" <span class='time'>(".format_time($Wl).")</span>".(strlen($z)<adminer()->sqlUrlLength()-100?" <a href='".h($z)."'>".lang(14)."</a>":"");$ta=connection()->affected_rows;$ho=($_POST["only_errors"]?"":driver()->warnings());$io="warnings-$Kb";if($ho)$Om
.=", <a href='#$io' class='toggle'>".lang(55)."</a>";$Nd="";$Od="explain-$Kb";if(is_object($G)){$y=$_POST["limit"];$gi=$y;$jd=!$_POST["only_errors"];if($jd)echo"<form action='' method='post'>\n";$Oi=print_select_result($G,$h,array(),$gi,$jd);if(!$_POST["only_errors"]){$gi=max($G->num_rows,$gi);echo"<p class='sql-footer'>".($gi?($y&&$gi>$y?lang(178,$y):"").lang(179,$gi):""),$Om;if($h&&preg_match("~^($Nl|\\()*+SELECT\\b~i",$fk)&&($Nd=adminer()->explain($h,$fk,$Oi))!="")echo", <a href='#$Od' class='toggle'>Explain</a>";if($jd)echo", <input type='submit' name='save' value='".lang(18)."' class='jsonly' disabled"." title='".lang(180)."'".on('click','sqlSave',lang(21)).">";$s="export-$Kb";echo", <a href='#$s' class='toggle'>".lang(85)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ra["output"])." ".html_select("format",adminer()->dumpFormat(),$ra["format"]).input_hidden("query",$fk)."<input type='submit' name='export' value='".lang(85)."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Nl*+(CREATE|DROP|ALTER)$Nl++(DATABASE|SCHEMA)\\b~i",$fk)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(181,$ta)."$Om\n";}echo($ho?"<div id='$io' class='hidden'>\n$ho</div>\n":""),($Nd!=""?"<div id='$Od' class='hidden explain'>\n$Nd</div>\n":"");}$Wl=microtime(true);}while(connection()->next_result());}}}}}if($sd)echo"<p class='message'>".lang(182)."\n";else{$Af=connection()->inTransaction();driver()->rollback();if($Af)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(183,$Kb-count($Cd))," <span class='time'>(".format_time($an).")</span>\n";elseif($Cd&&$Kb>1)echo"<p class='error'>".lang(177).": ".implode("",$Cd)."\n";}}else
echo"<p class='error'>".upload_error($F)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$In="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($In);echo'>
';$Kd="<input type='submit' value='".lang(184)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$fk=$_GET["sql"];if($_POST)$fk=$_POST["query"];elseif($_GET["history"]=="all")$fk=$lf;elseif($_GET["history"]!="")$fk=idx($lf[$_GET["history"]],0);echo"<p>";textarea("query",$fk,20,80,JUSH,$Ad);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$Kd\n",lang(185).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Te=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(186)."</legend><div>",($In?input_hidden(ini_get("session.upload_progress.name"),$In):""),"SQL$Te: ".file_input(" name='sql_file[]' multiple","\n$Kd"),($In?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$yf=adminer()->importServerPath();if($yf)echo"<fieldset><legend>".lang(187)."</legend><div>",lang(188,"<code>".h($yf)."$Te</code>")," <input type='submit' name='webfile' value='".lang(189)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(190))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(191))."\n",input_token();if(!isset($_GET["import"])&&$lf){print_fieldset("history",lang(192),$_GET["history"]!="");for($W=end($lf);$W;$W=prev($lf)){$w=key($lf);list($fk,$Om,$od)=$W;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(14)."</a>"." <span class='time' title='".@date('Y-m-d',$Om)."'>".@date("H:i:s",$Om)."</span>"." <code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Jg).*~m",'',$fk))),80,"</code>").($od?" <span class='time'>($od)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(193)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(194)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$o=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$o):""):where($_GET,$o));$Fn=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($o
as$A=>$n){if((!$Fn&&!isset($n["privileges"]["insert"]))||adminer()->fieldName($n)=="")unset($o[$A]);}if($_POST&&!$m&&!isset($_GET["select"])){$Og=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$Og=($Fn?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$Og))$Og=ME."select=".url_escape($a);$v=indexes($a);$zn=unique_array($_GET["where"],$v);$kk="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Og,lang(195),driver()->delete($a,$kk,$zn?0:1));else{$N=array();foreach($o
as$A=>$n){$W=process_input($n);if($W!==false&&$W!==null)$N[idf_escape($A)]=$W;}if($Fn){if(!$N)redirect($Og);queries_redirect($Og,lang(196),driver()->update($a,$N,$kk,$zn?0:1));if(is_ajax()){page_headers();page_messages($m);exit;}}else{$G=driver()->insert($a,$N);$zg=($G?last_id($G):0);queries_redirect($Og,lang(197,($zg?" $zg":"")),$G);}}}$I=null;$F="";$Om="";if($Z){$L=array();$gl=array("*");foreach($o
as$A=>$n){if(isset($n["privileges"]["select"])){$Ha=($_POST["clone"]&&$n["auto_increment"]?"''":convert_field($n));$d=($Ha?"$Ha AS ":"").idf_escape($A);$L[]=$d;if($Ha)$gl[]=$d;}}$I=array();if(!support("table")){$L=array("*");$gl=$L;}if($L){$Wl=microtime(true);$G=driver()->select($a,$L,array($Z),$L,array(),(isset($_GET["select"])?2:1));$F=str_replace("SELECT ".implode(", ",$L),"SELECT ".implode(", ",$gl),driver()->query);$Om=format_time($Wl);if(!$G)$m=adminer()->error();else{$I=$G->fetch_assoc();if(!$I)$I=false;}if(isset($_GET["select"])&&(!$I||$G->fetch_assoc()))$I=null;}}if(!$o&&driver()->primary!=""){if(!$Z){$G=driver()->select($a,array("*"),array(),array("*"));$I=($G?$G->fetch_assoc():false);if(!$I)$I=array(driver()->primary=>"");}if($I){foreach($I
as$w=>$W){if(!$Z)$I[$w]=null;$o[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$Kj=array();foreach((array)$_POST["fields"]as$w=>$W)$Kj[bracket_escape($w,true)]=$W;$I=$Kj+($I?$I:array());}edit_form($a,$o,$I,$Fn,$m,$F,$Om);}elseif(isset($_GET["create"])){function
referencable_primary($jl){$H=array();foreach(table_status('',true)as$um=>$Q){if($um!=$jl&&!$Q["dependent"]&&fk_support($Q)){foreach(fields($um)as$n){if($n["primary"]){if($H[$um]){unset($H[$um]);break;}$H[$um]=$n;}}}}return$H;}$a=$_GET["create"];$nj=driver()->partitionBy;$sj=($nj&&$a!=""?driver()->partitionsInfo($a):array());$sk=referencable_primary($a);$te=array();foreach($sk
as$um=>$n)$te[str_replace("`","``",$um)."`".str_replace("`","``",$n["field"])]=$um;$Si=array();$R=array();$di=false;if($a!=""){$Si=fields($a);$R=table_status1($a);$di=(count($R)<2);}$Aa=($a==""||driver()->supportsAlterTable($R));$I=$_POST;$I["fields"]=(array)$I["fields"];if($I["auto_increment_col"])$I["fields"][$I["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$m)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($I["fields"])&&!$m){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(198),drop_tables(array($a)));else{$o=array();$za=array();$Mn=false;$re=array();$Ri=reset($Si);$va=" FIRST";foreach($I["fields"]as$n){$q=$te[$n["type"]];$qn=($q!==null?$sk[$q]:$n);if($n["field"]!=""){if(!$n["generated"])$n["default"]=null;$n["default_sql"]=($n["generated"]=="DEFAULT SQL");$bk=process_field($n,$qn);$za[]=array($n["orig"],$bk,$va);if(!$Ri||$bk!==process_field($Ri,$Ri)){$o[]=array($n["orig"],$bk,$va);if($n["orig"]!=""||$va||(JUSH=="sqlite"&&$n["comment"]!=""))$Mn=true;}if($q!==null)$re[idf_escape($n["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$te[$n["type"]],'source'=>array($n["field"]),'target'=>array($qn["field"]),'on_delete'=>$n["on_delete"],),object_name("FOREIGN",trim($I["name"]),array($n["field"])));$va=" AFTER ".idf_escape($n["field"]);}elseif($n["orig"]!=""){$Mn=true;$o[]=array($n["orig"]);}if($n["orig"]!=""){$Ri=next($Si);if(!$Ri)$va="";}}$qj=array();if(in_array($I["partition_by"],$nj)){foreach($I
as$w=>$W){if(preg_match('~^partition~',$w))$qj[$w]=$W;}foreach($qj["partition_names"]as$w=>$A){if($A==""){unset($qj["partition_names"][$w]);unset($qj["partition_values"][$w]);}}$qj["partition_names"]=array_values($qj["partition_names"]);$qj["partition_values"]=array_values($qj["partition_values"]);if($qj==$sj)$qj=array();}elseif(preg_match("~partitioned~",$R["Create_options"]))$qj=null;$mh=lang(199);if($a==""){cookie("adminer_engine",$I["Engine"]);$mh=lang(200);}$A=trim($I["name"]);$Og=ME.(support("table")?"table=":"select=").url_escape($A);$f=($I["Comment"]!=$R["Comment"]?$I["Comment"]:null);$vd=($I["Engine"]&&$I["Engine"]!=$R["Engine"]?$I["Engine"]:"");$G=alter_table($a,$A,(JUSH=="sqlite"&&($Mn||$re||$f!==null||$vd)?$za:$o),$re,$f,$vd,($I["Collation"]&&$I["Collation"]!=$R["Collation"]?$I["Collation"]:""),($I["Auto_increment"]!=""?number($I["Auto_increment"]):""),$qj);if($G&&!Queries::$queries&&$a!=""&&!$o&&!$re)redirect($Og);queries_redirect($Og,$mh,$G);}}$Yl=($a!=""?"alter":"create");page_header(($a!=""?lang(53):lang(86)),$m,array("table"=>$a),h($a),$di,doc_link(array('sql'=>"$Yl-table.html",'mariadb'=>($a!=""?"$Yl-table":""),'pgsql'=>"sql-$Yl"."table.html",'cockroach'=>"$Yl-table",'mssql'=>"t-sql/statements/$Yl-table-transact-sql",'sqlite'=>"lang_{$Yl}table.html",'oracle'=>"sqlrf/".strtoupper($Yl)."-TABLE.html",)));if(!$_POST){$un=driver()->types();$I=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($un["int"])?"int":(isset($un["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$I=$R;$I["name"]=$a;$I["fields"]=array();if(!$_GET["auto_increment"])$I["Auto_increment"]="";foreach($Si
as$n){if($n["generated"])$n["default"]=ltrim($n["default"]);$n["generated"]=$n["generated"]?:(isset($n["default"])?($n["default_sql"]?"DEFAULT SQL":"DEFAULT"):"");$I["fields"][]=$n;}if($nj){$I+=$sj;$I["partition_names"][]="";$I["partition_values"][]="";}}}$Gb=flat_collations();$wd=driver()->engines();foreach($wd
as$vd){if(!strcasecmp($vd,$I["Engine"])){$I["Engine"]=$vd;break;}}$Xg=max_input_vars(12,20);if($Xg){$if=(count($I["fields"])>$Xg?"":" hidden");echo"<p".($if?" id='max-fields' data-columns='$Xg'":"")." class='error$if'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(201).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($I["name"])."' autocapitalize='off'>\n",(!$Aa?h($R["Engine"])."\n":($wd?html_select("Engine",array(""=>"(".lang(202).")")+$wd,$I["Engine"],on('change','helpClose').on_help_value())."\n":""));if($Gb)echo"<datalist id='collations'>".optionlist($Gb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($I["Collation"])."' placeholder='(".lang(125).")'>\n");echo"<input type='submit' value='".lang(18)."'>\n";}if(support("columns")&&$Aa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($I["fields"],$Gb,"TABLE",$te);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(60).": <input type='number' name='Auto_increment' class='size' value='".h($I["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(203),on('click','columnShowClick',6),"jsonly");$Nb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$Nb,lang(59),on('click','editingCommentsClick',true),"jsonly").' ';$c=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($Nb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$c,$I["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$a)),'>
';if($nj&&(JUSH=='sql'||$a=="")){$oj=preg_match('~RANGE|LIST~',$I["partition_by"]);print_fieldset("partition",lang(205),$I["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$nj),$I["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($I["partition"])."'>)\n",lang(206).": <input type='number' name='partitions' class='size".($oj||!$I["partition_by"]?" hidden":"")."' value='".h($I["partitions"])."'>\n","<table id='partition-table'".($oj?"":" class='hidden'").">\n","<thead><tr><th>".lang(207)."<th>".lang(208)."<tbody>\n";foreach($I["partition_names"]as$w=>$W)echo'<tr>','<td><input name="partition_names[]" value="'.h($W).'" autocapitalize="off"'.($w==count($I["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($I["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Gf=array("PRIMARY","UNIQUE","INDEX");$R=table_status1($a,true);$Df=driver()->indexAlgorithms($R);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$R["Engine"]))$Gf[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$R["Engine"]))$Gf[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$R["Engine"]))$Gf[]="VECTOR";$v=indexes($a);$o=fields($a);$Uj=array();if(JUSH=="mongo"){$Uj=$v["_id_"];unset($Gf[0]);unset($v["_id_"]);}$I=$_POST;if($I)save_settings(array("index_options"=>$I["options"]));if($_POST&&!$m&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($I["indexes"]as$u){$A=$u["name"];if(in_array($u["type"],$Gf)){$e=array();$Fg=array();$Kc=array();$Bi=array();$Ef=(support("partial_indexes")?$u["partial"]:"");$Cf=(in_array($u["algorithm"],$Df)?$u["algorithm"]:"");$N=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$d){if($d!=""){$x=idx($u["lengths"],$w);$Ic=idx($u["descs"],$w);$Ai=idx($u["opclasses"],$w);$N[]=($o[$d]?idf_escape($d):$d).($x?"(".(+$x).")":"").($Ai!=""?" ".idf_escape($Ai):"").($Ic?" DESC":"");$e[]=$d;$Fg[]=($x?:null);$Kc[]=$Ic;$Bi[]="$Ai";}}$Ld=$v[$A];if($Ld){ksort($Ld["columns"]);ksort($Ld["lengths"]);ksort($Ld["descs"]);if($u["type"]==$Ld["type"]&&array_values($Ld["columns"])===$e&&(!$Ld["lengths"]||array_values($Ld["lengths"])===$Fg)&&array_values($Ld["descs"])===$Kc&&(!$Ld["opclasses"]||array_values($Ld["opclasses"])===$Bi)&&$Ld["partial"]==$Ef&&(!$Df||$Ld["algorithm"]==$Cf)&&!$Ld["invalid"]){unset($v[$A]);continue;}}if($e)$b[]=array($u["type"],$A,$N,$Cf,$Ef);}}foreach($v
as$A=>$Ld)$b[]=array($Ld["type"],$A,"DROP");if(!$b)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(209),alter_indexes($a,$b));}page_header(lang(156),$m,array("table"=>$a),h($a),false,doc_link(array('sql'=>"create-index.html",'pgsql'=>"sql-createindex.html",'cockroach'=>"create-index",'mssql'=>"t-sql/statements/create-index-transact-sql",'sqlite'=>"lang_createindex.html",'oracle'=>"sqlrf/CREATE-INDEX.html",)));$ce=array_keys($o);if($_POST["add"]){foreach($I["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$I["indexes"][$w]["columns"][]="";}$u=end($I["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$I["indexes"][]=array("columns"=>array(1=>""));}if(!$I){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$I["indexes"]=$v;}$Fg=(JUSH=="sql"||JUSH=="mssql");$Bi=driver()->indexOpclasses();$Cl=($_POST?$_POST["options"]:get_setting("index_options"));$Kh=array();foreach($Gf
as$T)$Kh[$T]=str_replace("{table}",$a,adminer()->namePattern($T));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(210);$wf=" class='idxopts".($Cl?"":" hidden")."'";if($Df)echo"<th id='label-algorithm'$wf>".lang(211).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/','pgsql'=>'indexes-types.html','cockroach'=>'create-index#parameters',));echo'<th><input type="submit" hidden>',lang(212).($Fg?"<span$wf> (".lang(213).")</span>":"");if($Fg||support("descidx"))echo
checkbox("options",1,$Cl,lang(131),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(214);if(support("partial_indexes"))echo"<th id='label-condition'$wf>".lang(215);echo'<td><noscript>',icon("plus","add[0]","+",lang(132)),'</noscript>
<tbody>
';if($Uj){echo"<tr><td>PRIMARY<td>";foreach($Uj["columns"]as$w=>$d)echo
select_input(" disabled",array_combine($ce,$ce),$d),"<label><input disabled type='checkbox'>".lang(68)."</label> ";echo"<td><td>\n";}$hg=1;foreach($I["indexes"]as$u){if(!$_POST["drop_col"]||$hg!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$hg][type]",array(-1=>"")+$Gf,$u["type"],on('change','indexesChangeType',$Kh),"label-type");if($Df)echo"<td$wf>".html_select("indexes[$hg][algorithm]",array_merge(array(""),$Df),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$d){echo"<span>".select_input(" name='indexes[$hg][columns][$r]' title='".lang(57)."'".on('change','indexesChangeColumn',$Kh),($o&&($d==""||$o[$d])?array_combine($ce,$ce):array()),$d)," <span$wf>",($Fg?"<input type='number' name='indexes[$hg][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(130)."'>":"");if($Bi){$Ai=idx($u["opclasses"],$w);echo
html_select("indexes[$hg][opclasses][$r]",array(""=>"(".lang(216).")")+array_combine($Bi,$Bi)+($Ai!=""?array($Ai=>$Ai):array()),$Ai),doc_link(array('pgsql'=>'indexes-opclass.html'));}echo(support("descidx")?checkbox("indexes[$hg][descs][$r]",1,idx($u["descs"],$w),lang(68)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$hg][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$wf><input name='indexes[$hg][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$hg]","x",lang(134),on('click','editingRemoveRow','indexes$1[type]')),(idx($v[$u["name"]],"invalid")?" <span class='error'>INVALID</span>":""),"\n";}$hg++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(support("concurrent_indexes"))echo
checkbox("concurrently",1,$_POST["concurrently"],"concurrently").doc_link(array('pgsql'=>'sql-createindex.html#SQL-CREATEINDEX-CONCURRENTLY'))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["database"])){$I=$_POST;if($_POST&&!$m&&!$_POST["add"]){$A=trim($I["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(217),drop_databases(array(DB)));}elseif($A!==DB){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($A),lang(218),rename_database($A,(string)$I["collation"]));}else{$j=explode("\n",str_replace("\r","",$A));$fm=true;$xg="";foreach($j
as$k){if(count($j)==1||$k!=""){if(!create_database($k,(string)$I["collation"]))$fm=false;$xg=$k;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($xg),lang(219),$fm);}}else{if(!$I["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(preg_match('~^[a-z0-9_]+$~i',$I["collation"])?" COLLATE $I[collation]":""),substr(ME,0,-1),lang(220));}}$Yl=(DB!=""?"alter":"create");page_header(DB!=""?lang(76):lang(138),$m,array(),h(DB),false,doc_link(array('sql'=>"$Yl-database.html",'mariadb'=>(DB!=""?"":"$Yl-database"),'pgsql'=>"sql-$Yl"."database.html",'cockroach'=>"$Yl-database",'mssql'=>"t-sql/statements/$Yl-database-transact-sql",'oracle'=>"sqlrf/".strtoupper($Yl)."-USER.html",)));$Gb=collations();$A=DB;if($_POST)$A=$I["name"];elseif(DB!="")$I["collation"]=db_collation(DB,$Gb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Je){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Je,$_)&&$_[1]){$A=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($A,"\n")?"<textarea autofocus name='name' rows='10' cols='40'>\n".h($A).'</textarea><br>':'<input name="name" autofocus value="'.h($A).'" data-maxlength="64" autocapitalize="off">')."\n",($Gb?"<datalist id='collations'>".optionlist(flat_collations())."</datalist>"."<input list='collations' name='collation' value='".h($I["collation"])."' placeholder='(".lang(125).")'>".doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):"")."\n",'<input type=\'submit\' value=\'',lang(18),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(150)."'".confirm(lang(204,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(132))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$I=$_POST;$f=(support("comment")&&$_GET["ns"]!=""?schema_comment($_GET["ns"]):"");if($_POST&&!$m){$z=preg_replace('~&ns=[^&]*~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$z,lang(221));else{$A=trim($I["name"]);$z
.=url_escape($A);$Rh=(support("comment")?$I["comment"]:$f);$G=true;$mh=lang(222);if($_GET["ns"]==""){$G=queries("CREATE SCHEMA ".idf_escape($A));$mh=lang(223);}elseif($_GET["ns"]!=$A)$G=queries("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($A));elseif($Rh===$f)redirect($z);if($G&&$Rh!==$f)$G=set_schema_comment($A,$Rh);queries_redirect($z,$mh,$G);}}$Yl=($_GET["ns"]!=""?"alter":"create");page_header($_GET["ns"]!=""?lang(77):lang(78),$m,array(),"",false,doc_link(array('pgsql'=>"sql-$Yl"."schema.html",'cockroach'=>"$Yl-schema",'mssql'=>"t-sql/statements/create-schema-transact-sql",)));if(!$I){$I["name"]=$_GET["ns"];$I["comment"]=$f;}echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'',lang(18),'\'>
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".lang(150)."'".confirm(lang(204,$_GET["ns"])).">\n";echo(support("comment")?"<p>".lang(59).": ".adminer()->commentInput('SCHEMA'," name='comment'",$I["comment"])."\n":""),input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$Qk=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Lk=routine($_GET["call"],$Qk);$eg=(idx($Lk["returns"],"type")=="trigger");page_header(($eg?lang(164):lang(224)).": ".h($ca),$m,"#routines","",!$Lk,(isset($_GET["callf"])?"":doc_link(array('sql'=>"call.html",'pgsql'=>"sql-call.html",'cockroach'=>"call",'mssql'=>"t-sql/language-elements/execute-transact-sql",))));$zf=array();$Yi=array();foreach($Lk["fields"]as$r=>$n){if(substr($n["inout"],-3)=="OUT"&&JUSH=='sql')$Yi[$r]="@".idf_escape($n["field"])." AS ".idf_escape($n["field"]);if(!$n["inout"]||preg_match('~^(IN|OUTPUT)~',$n["inout"]))$zf[]=$r;}if(!$m&&$_POST){$jb=array();foreach($Lk["fields"]as$w=>$n){$W="";if(in_array($w,$zf)){$W=process_input($n);if($W===false)$W="''";if(isset($Yi[$w]))connection()->query("SET @".idf_escape($n["field"])." = $W");}if(isset($Yi[$w]))$jb[]="@".idf_escape($n["field"]);elseif(in_array($w,$zf))$jb[]=$W;}$Fa=implode(", ",$jb);$F=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Lk["returns"],"type")=="record"?"* FROM ":"").table($ca)."($Fa)":"EXEC ".table($ca).($Fa!=""?" $Fa":""));$Wl=microtime(true);$G=connection()->multi_query($F);$ta=connection()->affected_rows;echo
adminer()->selectQuery($F,$Wl,!$G);if(!$G)echo"<p class='error'>".adminer()->error()."\n";else{$h=connect();if($h)$h->select_db(DB);do{$G=connection()->store_result();if(is_object($G))print_select_result($G,$h);else
echo"<p class='message'>".lang(225,$ta)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Yi)print_select_result(connection()->query("SELECT ".implode(", ",$Yi)));}}if($eg){$nn=driver()->routineTriggers($_GET["call"]);if($nn){echo"<table>\n";foreach($nn
as$I){$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($I["ns"]),ME);echo"<tr><th>".h($I["trigger"])."<td><a href='".h($z."table=".url_escape($I["table"])."#triggers")."'>".($I["ns"]!=$_GET["ns"]?"<b>".h($I["ns"])."</b>.":"").h($I["table"])."</a>"."<td class='hover'><a href='".h($z."trigger=".url_escape($I["table"])."&name=".url_escape($I["trigger"]))."'>".lang(160)."</a>\n";}echo"</table>\n";}}else{echo'<form action="" method="post">
';if($zf){echo"<table class='layout'>\n";foreach($zf
as$w){$n=$Lk["fields"][$w];$A=$n["field"];echo"<tr><th>".adminer()->fieldName($n);$X=idx($_POST["fields"],$A);if($X!=""){if($n["type"]=="set")$X=implode(",",$X);}input($n,$X,idx($_POST["function"],$A,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(224),'\'>
',input_token(),'</form>
';}echo'
',adminer()->commentValue($Qk,$Lk['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$I=$_POST;if($_POST&&!$m&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$I["source"]=array_filter($I["source"],'strlen');ksort($I["source"]);$Fm=array();foreach($I["source"]as$w=>$W)$Fm[$w]=$I["target"][$w];$I["target"]=$Fm;}$Ya=false;if(JUSH=="sqlite")$G=recreate_table($a,$a,array(),array(),array(" $A"=>($I["drop"]?"":" ".format_foreign_key($I))));else{$Wb=$I["constraint"];$b="ALTER TABLE ".table($a);$cd="$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A?:'');if($I["drop"])$G=queries($cd);else{$ma=" ADD".format_foreign_key($I,$Wb);if($A=="")$G=queries($b.$ma);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Wb!=$A))$G=queries("$cd,$ma");else{$Ya=support("transaction_ddl")&&driver()->begin();$G=($Ya||idx(foreign_keys($a),$A)?queries($cd):true)&&queries($b.$ma)&&(!$Ya||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($I["drop"]?lang(226):($A!=""?lang(227):lang(228))),$G);if($Ya)driver()->rollback();if(!$I["drop"])$m=lang(229);}$di=false;if(!$_POST&&$A!=""){$te=foreign_keys($a);$I=idx($te,$A,array());$di=!$I;}$Jh=str_replace("{table}",$a,adminer()->namePattern("FOREIGN"));$Mh="^".str_replace('\{columns\}','.*',preg_quote($Jh))."$";page_header(($A!=""?lang(230):lang(161)),$m,array("table"=>$a),h($A!=""?$A:$a),$di,doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-PARMS-REFERENCES",'cockroach'=>"foreign-key",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"sqlrf/constraint.html",)));if($_POST){ksort($I["source"]);if($_POST["change"]||$_POST["change-js"])$I["target"]=array();else$I["source"][]="";}elseif($A!=""){$I["source"][]="";$I["constraint"]=$A;}else{$I["table"]=$a;$I["source"]=array("");$I["constraint"]=str_replace("{columns}","",$Jh);}echo'
<form action="" method="post">
';$Ll=array_keys(fields($a));if($I["db"]!="")connection()->select_db($I["db"]);$Ti=null;if($I["ns"]!=""){$Ti=get_schema();set_schema($I["ns"]);}$rk=array_keys(array_filter(table_status('',true),function(array$R){return!$R["dependent"]&&fk_support($R);}));$Fm=array_keys(fields(in_array($I["table"],$rk)?$I["table"]:reset($rk)));$c=on('change','foreignChange');echo"<p><label>".lang(231).": ".html_select("table",$rk,$I["table"],$c)."</label>\n";if(support("scheme")){$ei=($I["ns"]!=""?$I["ns"]:$_GET["ns"]);$Xk=array_filter(adminer()->schemas(),function($K)use($ei){return!driver()->isSystem(DB,$K)||$K==$ei||$K==$_GET["ns"];});echo"<label>".lang(88).": ".html_select("ns",$Xk,$ei,$c)."</label>";if($Ti!==null)set_schema($Ti);}elseif(JUSH!="sqlite"){$hl=($I["db"]!=""?$I["db"]:DB);$yc=array();foreach(adminer()->databases()as$k){if(!driver()->isSystem($k)||$k==$hl||$k==DB)$yc[]=$k;}echo"<label>".lang(87).": ".html_select("db",$yc,$hl,$c)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(232),'\'></noscript>
<table',on('change','foreignName',$Mh,$Jh),'>
<thead><tr><th id="label-source">',lang(158),'<th id="label-target">',lang(159),'<tbody>
';$hg=0;foreach($I["source"]as$w=>$W){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Ll,$W,($hg==count($I["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$Fm,idx($I["target"],$w),"","label-target");$hg++;}echo'</table>
<p>
<label>',lang(127),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$I["on_delete"]),'</label>
<label>',lang(126),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$I["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$I["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(233),'\'></noscript>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$A)),'>
';if(JUSH!="sqlite"){print_fieldset("options",lang(131),$I["constraint"]!=$A&&!preg_match("($Mh)",$I["constraint"]));echo"<label>".lang(214).": <input name='constraint' value='".h($I["constraint"])."' autocapitalize='off'></label>\n","</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$I=$_POST;$Ui="VIEW";if(JUSH=="pgsql"&&$a!=""){$O=table_status1($a);$Ui=strtoupper($O["Engine"]);}if($_POST&&!$m){$A=trim($I["name"]);$Ha=" AS\n$I[select]";$Og=ME."table=".url_escape($A);$mh=lang(234);$T=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$A&&JUSH!="sqlite"&&$T=="VIEW"&&$Ui=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$Ha,$Og,$mh);else{$Jm="adminer_".uniqid();drop_create("DROP $Ui ".table($a),"CREATE $T ".table($A).$Ha,"DROP $T ".table($A),"CREATE $T ".table($Jm).$Ha,"DROP $T ".table($Jm),($_POST["drop"]?substr(ME,0,-1):$Og),lang(235),$mh,lang(236),$a,$A);}}$di=false;if(!$_POST&&$a!=""){$I=view($a);$di=!$I["select"];$I["name"]=$a;$I["materialized"]=($Ui!="VIEW");if(!$m)$m=adminer()->error();}page_header(($a!=""?lang(52):lang(237)),$m,array("table"=>$a),h($a),$di,doc_link(array('sql'=>"create-view.html",'pgsql'=>"sql-createview.html",'cockroach'=>"create-view",'mssql'=>"t-sql/statements/create-view-transact-sql",'sqlite'=>"lang_createview.html",'oracle'=>"sqlrf/CREATE-VIEW.html",)));echo'
<form action="" method="post">
<p>',lang(214),': <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$I["materialized"],lang(152)):""),'<p>';textarea("select",$I["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Uf=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$am=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$I=$_POST;if($_POST&&!$m){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(238));elseif(in_array($I["INTERVAL_FIELD"],$Uf)&&isset($am[$I["STATUS"]])){$Vk="\nON SCHEDULE ".($I["INTERVAL_VALUE"]?"EVERY ".q($I["INTERVAL_VALUE"])." $I[INTERVAL_FIELD]".($I["STARTS"]?" STARTS ".q($I["STARTS"]):"").($I["ENDS"]?" ENDS ".q($I["ENDS"]):""):"AT ".q($I["STARTS"]))." ON COMPLETION".($I["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(239):lang(240)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Vk.($aa!=$I["EVENT_NAME"]?"\nRENAME TO ".idf_escape($I["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($I["EVENT_NAME"]).$Vk)."\n".$am[$I["STATUS"]]." COMMENT ".q($I["EVENT_COMMENT"]).rtrim(" DO\n$I[EVENT_DEFINITION]",";").";"));}}$di=false;if(!$I&&$aa!=""){$J=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$di=!$J;$I=reset($J);}page_header(($aa!=""?lang(241).": ".h($aa):lang(242)),$m,"#events","",$di,doc_link(array('sql'=>"create-event.html")));echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(214),'<td><input name="EVENT_NAME" value="',h($I["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(243),'<td><input name="STARTS" value="',h("$I[EXECUTE_AT]$I[STARTS]"),'">
<tr><th title="datetime">',lang(244),'<td><input name="ENDS" value="',h($I["ENDS"]),'">
<tr><th>',lang(245),'<td><input type="number" name="INTERVAL_VALUE" value="',h($I["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Uf,$I["INTERVAL_FIELD"]),'<tr><th>',lang(141),'<td>',html_select("STATUS",$am,$I["STATUS"]),'<tr><th>',lang(59),'<td><input name="EVENT_COMMENT" value="',h($I["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$I["ON_COMPLETION"]=="PRESERVE",lang(246)),'</table>
<p>';textarea("EVENT_DEFINITION",$I["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$Lk=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$I=$_POST;$I["fields"]=(array)$I["fields"];if($_POST&&!process_fields($I["fields"])&&!$m){foreach($I["fields"]as$w=>$n){if($n["field"]=="")unset($I["fields"][$w]);}$ui=routine($_GET["procedure"],$Lk);$si=($ui?routine_id($ca,$ui):"");$Th=routine_id($I["name"],$I);$i=create_routine($Lk,$I);$Og=substr(ME,0,-1);$mh=lang(247);if(!$_POST["drop"]&&$si==$Th&&connection()->flavor!="mysql")queries_redirect($Og,$mh,queries(substr_replace($i,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$Jm="adminer_".uniqid();drop_create("DROP $Lk $si",$i,"DROP $Lk $Th",create_routine($Lk,array("name"=>$Jm)+$I),"DROP $Lk ".routine_id($Jm,$I),$Og,lang(248),$mh,lang(249),$ca,$I["name"]);}}$di=false;if(!$_POST&&$ca!=""){$I=routine($_GET["procedure"],$Lk);$di=!$I;$I["name"]=$ca;}$Ok=strtolower($Lk);page_header(($ca!=""?(isset($_GET["function"])?lang(165):lang(250)).": ".h($ca):(isset($_GET["function"])?lang(251):lang(252))),$m,"#routines","",$di,doc_link(array('sql'=>"create-procedure.html",'mariadb'=>"create-$Ok/",'pgsql'=>"sql-create$Ok.html",'cockroach'=>"create-$Ok",'mssql'=>"t-sql/statements/create-$Ok-transact-sql",)));if(!$_POST&&$ca=="")$I["language"]="sql";$Gb=(JUSH=="sql"?flat_collations():array());$Mk=routine_languages();echo($Gb?"<datalist id='collations'>".optionlist($Gb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(214),': <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',($Mk?"<label>".lang(24).": ".html_select("language",array_keys($Mk),$I["language"],on('change','routineLanguage',$Mk))."</label>\n":""),'<input type=\'submit\' value=\'',lang(18),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($I["fields"],$Gb,$Lk);if(isset($_GET["function"])){echo"<tr><td>".lang(253);edit_type("returns",(array)$I["returns"],$Gb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$I["definition"],20,80,($Mk[$I["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$ca)),'>
';$Pk=routine_options($Lk);if($Pk){$Ii=false;foreach($Pk
as$w=>$Y){$l=($Y?reset($Y):"");$I["options"][$w]=idx($I["options"],$w,$l);if($I["options"][$w]!=$l)$Ii=true;}print_fieldset("options",lang(131),$Ii);echo"<table class='layout'>\n";foreach($Pk
as$w=>$Y){$sg="label-option-$w";$Rm=str_replace("_"," ",$w);$L=array();foreach($Y
as$X)$L[$X]=(strpos($X,"$Rm ")===0?substr($X,strlen($Rm)+1):$X);echo"<tr><th id='$sg'>$Rm<td>".($L?html_select("options[$w]",$L,$I["options"][$w],"",$sg):"<input name='options[$w]' value='".h($I["options"][$w])."' aria-labelledby='$sg' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$ea=$_GET["sequence"];$I=$_POST;if($_POST&&!$m){$z=substr(ME,0,-1);$A=trim($I["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($ea),$z,lang(254));elseif($ea=="")query_redirect("CREATE SEQUENCE ".idf_escape($A),$z,lang(255));elseif($ea!=$A)query_redirect("ALTER SEQUENCE ".idf_escape($ea)." RENAME TO ".idf_escape($A),$z,lang(256));else
redirect($z);}$di=(!$_POST&&$ea!=""&&!get_val("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." AND relname = ".q($ea)));page_header(($ea!=""?lang(257).": ".h($ea):lang(258)),$m,"#sequences","",$di,doc_link(array('pgsql'=>"sql-createsequence.html",'cockroach'=>"create-sequence",)));if(!$I)$I["name"]=$ea;echo'
<form action="" method="post">
<p><input name="name" value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ea!="")echo"<input type='submit' name='drop' value='".lang(150)."'".confirm(lang(204,$ea)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){function
enum_values($Ec){$X="'(?:[^']|'')*'";if(!preg_match('~^AS\s+ENUM\s*\(\s*('.$X.'(?:\s*,\s*'.$X.')*)\s*\)$~i',$Ec,$_))return
null;preg_match_all('~'.$X.'~',$_[1],$Ug);return$Ug[0];}function
add_enum_values($T,$qi,$Qh){$wi=enum_values($qi);$Yh=enum_values($Qh);if($wi===null||$Yh===null)return
null;$H=array();$r=0;foreach($Yh
as$X){if($X===idx($wi,$r))$r++;else$H[]="ALTER TYPE ".idf_escape($T)." ADD VALUE $X".($r<count($wi)?" BEFORE ".$wi[$r]:"");}return($r==count($wi)?$H:null);}$fa=$_GET["type"];$I=$_POST;$rn=($fa!=""?array_search($fa,types(true)):0);$T=($rn?type_definition(+$rn):array());$ii=($T["kind"]=='d'?"DOMAIN":"TYPE");if($_POST&&!$m){$z=substr(ME,0,-1);$A=trim($I["name"]);$Ha=trim(str_replace("\r","",$I["as"]));$Vh=(preg_match('~^AS\s+(?!ENUM\b|RANGE\b|\()~i',$Ha)?"DOMAIN":"TYPE");$mh=lang(259);$b=(!$_POST["drop"]&&$fa!=""&&$Vh==$ii?($Ha==$T["definition"]?array():add_enum_values($fa,$T["definition"],$Ha)):null);if($b!==null){if($fa!=$A)$b[]="ALTER $ii ".idf_escape($fa)." RENAME TO ".idf_escape($A);if(!$b)redirect($z);$Vd=false;foreach($b
as$F){if(!queries($F)){$Vd=true;break;}}queries_redirect($z,$mh,!$Vd);}else{$Jm="adminer_".uniqid();drop_create("DROP $ii ".idf_escape($fa),"CREATE $Vh ".idf_escape($A)." $Ha","DROP $Vh ".idf_escape($A),"CREATE $Vh ".idf_escape($Jm)." $Ha","DROP $Vh ".idf_escape($Jm),$z,lang(260),$mh,lang(261),$fa,$A);}}page_header(($fa!=""?lang(262).": ".h($fa):lang(263)),$m,"#user-types","",($rn===false),doc_link(array('pgsql'=>"sql-createtype.html",'cockroach'=>"create-type",)));if(!$I){$I["name"]=$fa;$I["as"]=($fa!=""?$T["definition"]:"AS ");}echo'
<form action="" method="post">
<p>
',lang(214).": <input name='name' value='".h($I['name'])."' autocapitalize='off'>\n";textarea("as",$I["as"]);echo"<p><input type='submit' value='".lang(18)."'>\n";if($fa!="")echo"<input type='submit' name='drop' value='".lang(150)."'".confirm(lang(204,$fa)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$A="$_GET[name]";$I=$_POST;if($I&&!$m){$Og=ME."table=".url_escape($a);$ph=lang(264);$nh=lang(265);$oh=lang(266);if(JUSH=="sqlite"&&!(min_version(3.53)&&($A==""||idx(driver()->checkConstraints($a),$A)!=$A))){$ma=($I["drop"]?"":($I["name"]!=""?"CONSTRAINT ".idf_escape($I["name"])." ":"")."CHECK ($I[clause])");queries_redirect($Og,($I["drop"]?$ph:($A!=""?$nh:$oh)),recreate_table($a,$a,array(),array(),array(),"",array(),"$A",$ma));}else{$b="ALTER TABLE ".table($a);$rb=" CHECK ($I[clause])";$Jm="adminer_".uniqid();drop_create("$b DROP CONSTRAINT ".idf_escape($A),"$b ADD".($I["name"]!=""?" CONSTRAINT ".idf_escape($I["name"]):"").$rb,"$b DROP CONSTRAINT ".idf_escape($I["name"]),"$b ADD CONSTRAINT ".idf_escape($Jm).$rb,"$b DROP CONSTRAINT ".idf_escape($Jm),$Og,$ph,$nh,$oh,$A,$I["name"]);}}$di=false;if(!$I){$vb=driver()->checkConstraints($a);$di=($A!=""&&!$vb[$A]);$I=array("name"=>($A==""?object_name("CHECK",$a,array()):($vb[$A]!=$A?$A:"")),"clause"=>$vb[$A]);}page_header(($A!=""?lang(267):lang(163)),$m,array("table"=>$a),h($A!=""?$A:$a),$di,doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'cockroach'=>"check",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",)));echo'
<form action="" method="post">
<p><label>',lang(214),': <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off"></label>
<p>';textarea("clause",$I["clause"]);echo'<p><input type=\'submit\' value=\'',lang(18),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A="$_GET[name]";$mn=trigger_options();$I=trigger($A,$a);$di=($A!=""&&!$I);$Jh=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$I+=array("Trigger"=>strtr($Jh,array("{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row")));if($_POST){if(!$m&&in_array($_POST["Timing"],$mn["Timing"])&&in_array($_POST["Event"],$mn["Event"])&&in_array($_POST["Type"],$mn["Type"])){$xi=" ON ".table($a);$cd="DROP TRIGGER ".idf_escape($A).(JUSH=="pgsql"?$xi:"");$Og=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($cd,$Og,lang(268));else{if($A!="")queries($cd);queries_redirect($Og,($A!=""?lang(269):lang(270)),queries(create_trigger($xi,$_POST)));if($A!="")queries(create_trigger($xi,$I+array("Type"=>reset($mn["Type"]))));}}$I=$_POST;}page_header(($A!=""?lang(271):lang(166)),$m,array("table"=>$a),h($A!=""?$A:$a),$di,doc_link(array('sql'=>"create-trigger.html",'pgsql'=>"sql-createtrigger.html",'cockroach'=>"create-trigger",'mssql'=>"t-sql/statements/create-trigger-transact-sql",'sqlite'=>"lang_createtrigger.html",'oracle'=>"lnpls/CREATE-TRIGGER-statement.html",)));$Mh=strtr(preg_quote($Jh),array('\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)'));$kn=on('change','triggerChange',"^$Mh$",$Jh);$li=on('input','triggerChange',"^$Mh$",$Jh);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(272),'<td>',html_select("Timing",$mn["Timing"],$I["Timing"],$kn),'<tr><th>',lang(273),'<td>',html_select("Event",$mn["Event"],$I["Event"],$kn),(in_array("UPDATE OF",$mn["Event"])?" <input name='Of' value='".h($I["Of"])."' class='hidden'$li>":""),'<tr><th>',lang(58),'<td>',html_select("Type",$mn["Type"],$I["Type"],$kn),'<tr><th>',lang(214),'<td><input name="Trigger" value="',h($I["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$I["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Je,array$Zj,$e,$xi){if(!$Zj)return
true;if($Zj==array("ALL PRIVILEGES","GRANT OPTION"))return($Je=="GRANT"?queries("$Je ALL PRIVILEGES$xi WITH GRANT OPTION"):queries("$Je ALL PRIVILEGES$xi")&&queries("$Je GRANT OPTION$xi"));return
queries("$Je ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$Zj).$e).$xi);}$ga=$_GET["user"];$Zj=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$I){foreach(explode(",",($I["Privilege"]=="Grant option"?"":$I["Context"]))as$bc)$Zj[$bc=="File access on server"?"Server Admin":$bc][$I["Privilege"]]=$I["Comment"];}unset($Zj["Server Admin"]["Usage"]);foreach($Zj["Tables"]as$w=>$W)unset($Zj["Databases"][$w]);$Sh=array();if($_POST){foreach($_POST["objects"]as$w=>$W)$Sh[$W]=(array)$Sh[$W]+idx($_POST["grants"],$w,array());}$Ke=array();$G=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ga)."@".q($_GET["host"])):null);$di=(isset($_GET["host"])&&!$G);if($G){while($I=$G->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$I[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$_[1],$Ug,PREG_SET_ORDER)){foreach($Ug
as$W){if($W[1]!="USAGE")$Ke["$_[2]$W[2]"][$W[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$I[0]))$Ke["$_[2]$W[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$m){$vi=(isset($_GET["host"])?q($ga)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $vi",ME."privileges=",lang(274));else{$Xh=q($_POST["user"])."@".q($_POST["host"]);$uj=$_POST["pass"];$jc=false;$G=true;if($vi!=$Xh){$jc=queries("CREATE USER $Xh IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($uj));$G=$jc;}elseif($uj!="")$G=queries("SET PASSWORD FOR $Xh = ".(min_version(8,99)||$_POST["hashed"]?q($uj):"PASSWORD(".q($uj).")"));if($G){$Hk=array();foreach($Sh
as$ii=>$Je){if(isset($_GET["grant"]))$Je=array_filter($Je);$Je=array_keys($Je);if(isset($_GET["grant"]))$Hk=array_diff(array_keys(array_filter($Sh[$ii],'strlen')),$Je);elseif($vi==$Xh){$ri=array_keys((array)$Ke[$ii]);$Hk=array_diff($ri,$Je);$Je=array_diff($Je,$ri);unset($Ke[$ii]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$ii,$_)&&(!grant("REVOKE",$Hk,$_[2]," ON $_[1] FROM $Xh")||!grant("GRANT",$Je,$_[2]," ON $_[1] TO $Xh"))){$G=false;break;}}}if($G&&isset($_GET["host"])){if($vi!=$Xh)queries("DROP USER $vi");elseif(!isset($_GET["grant"])){foreach($Ke
as$ii=>$Hk){if(preg_match('~^(.+)(\(.*\))?$~U',$ii,$_))grant("REVOKE",array_keys($Hk),$_[2]," ON $_[1] FROM $Xh");}}}if($G&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(275):lang(276)),$G);if($jc)connection()->query("DROP USER $Xh");}}page_header((isset($_GET["host"])?lang(42).": ".h("$ga@$_GET[host]"):lang(174)),$m,array("privileges"=>array('',lang(80))),"",$di,doc_link(array('sql'=>"grant.html",'mariadb'=>"grant")));$I=$_POST;if($I)$Ke=$Sh;else{$I=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Ke[(DB==""||$Ke?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(40),'<td><input name="host" data-maxlength="60" value="',h($I["host"]),'" autocapitalize="off">
<tr><th>',lang(42),'<td><input name="user" data-maxlength="80" value="',h($I["user"]),'" autocapitalize="off">
<tr><th>',lang(43),'<td><input name="pass" id="pass" value="',h($I["pass"]),'" autocomplete="new-password">
',($I["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$I["hashed"],lang(277),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(80);$r=0;foreach($Ke
as$ii=>$Je){echo'<th>'.($ii!="*.*"?"<input name='objects[$r]' value='".h($ii)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(40),"Databases"=>lang(44),"Tables"=>lang(154),"Procedures"=>lang(278),)as$bc=>$Ic){foreach((array)$Zj[$bc]as$Yj=>$f){echo"<tr><td".($Ic?">$Ic<td":" colspan='2'").' lang="en" title="'.h($f).'">'.h($Yj);$r=0;foreach($Ke
as$ii=>$Je){$A="'grants[$r][".h(strtoupper($Yj))."]'";$X=$Je[strtoupper($Yj)];if($bc=="Server Admin"&&$ii!=(isset($Ke["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$A><option><option value='1'".($X?" selected":"").">".lang(279)."<option value='0'".($X=="0"?" selected":"").">".lang(280)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$A value='1'".($X?" checked":"").($Yj=="All privileges"?" id='grants-$r-all'":($Yj=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(150),'\'',confirm(lang(204,"$ga@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$m){$pg=0;foreach((array)$_POST["kill"]as$W){if(adminer()->killProcess($W))$pg++;}queries_redirect(ME."processlist=",lang(281,$pg),$pg||!$_POST["kill"]);}}page_header(lang(139),$m);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$I){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($I
as$w=>$W)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"refrn/V-SESSION.html",));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$I[JUSH=="sql"?"Id":"pid"],0):"");foreach($I
as$w=>$W)echo"<td>".($W!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$I["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='sql jush-".JUSH."' data-full='".h($W)."'>".shorten_utf8($W,100,"</code>").' <a href="'.h(($I["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($I["db"])."&":ME)."sql=".url_escape($W)).'">'.lang(282).'</a>'.' '.copy_icon():h($W));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($r+1)."/".lang(283,max_connections()),"<p><input type='submit' value='".lang(284)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$R=table_status1($a);$v=indexes($a);$o=fields($a);$te=column_foreign_keys($a);$pi=$R["Oid"];$Jk=array();$e=array();$bl=array();$Li=array();$Mm=null;foreach($o
as$w=>$n){$A=adminer()->fieldName($n);$Lh=html_entity_decode(strip_tags($A),ENT_QUOTES);if(isset($n["privileges"]["select"])&&$A!=""){$e[$w]=$Lh;if(is_shortable($n))$Mm=adminer()->selectLengthProcess();}if(isset($n["privileges"]["where"])&&$A!="")$bl[$w]=$Lh;if(isset($n["privileges"]["order"])&&$A!="")$Li[$w]=$Lh;$Jk+=$n["privileges"];}list($L,$Me)=adminer()->selectColumnsProcess($e,$v);$L=array_unique($L);$Me=array_unique($Me);$bg=count($Me)<count($L);$Z=adminer()->selectSearchProcess($o,$v,$R);$Ki=adminer()->selectOrderProcess($o,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$_n=>$I){$Ha=convert_field($o[key($I)]);$L=array($Ha?:idf_escape(key($I)));$Z[]=where_check(bracket_escape($_n,true),$o);$H=driver()->select($a,$L,$Z,$L);if($H)echo
first($H->fetch_row());}exit;}$Uj=$Cn=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$Uj=array_flip($u["columns"]);$Cn=($L?$Uj:array());foreach($Cn
as$w=>$W){if(in_array(idf_escape($w),$L))unset($Cn[$w]);}break;}}if($pi&&!$Uj){$Uj=$Cn=array($pi=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($pi));}if($_POST&&!$m){$ko=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$vb=array();foreach($_POST["check"]as$rb)$vb[]=where_check($rb,$o);$ko[]="((".implode(") OR (",$vb)."))";}$mo=$ko;$ko=($ko?"\nWHERE ".implode(" AND ",$ko):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$fl=($L?:array("*"));$ec=convert_fields($e,$o,$L);if($ec)$fl[]=substr($ec,2);$F="";if(is_array($_POST["check"])&&!$Uj){$_e=implode(", ",$fl)."\nFROM ".table($a);$Oe=($Me&&$bg?"\nGROUP BY ".implode(", ",$Me):"").($Ki?"\nORDER BY ".implode(", ",$Ki):"");$xn=array();foreach($_POST["check"]as$W)$xn[]="(SELECT".limit($_e,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$o).$Oe,1).")";$F=implode(" UNION ALL ",$xn);}adminer()->dumpData($a,"table",$F,$fl,$mo,($bg?$Me:array()),$Ki);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$te)){if($_POST["save"]||$_POST["delete"]){$G=true;$ta=0;$Ya=false;$N=array();if(!$_POST["delete"]){foreach($o
as$A=>$W){$t=bracket_escape($A);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$W=process_input($o[$A]);if($W!==null&&($_POST["clone"]||$W!==false))$N[idf_escape($A)]=($W!==false?$W:idf_escape($A));}}}if($_POST["delete"]||$N){$F=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($N)).")\nSELECT ".implode(", ",$N)."\nFROM ".table($a):"");if($_POST["all"]||($Uj&&is_array($_POST["check"]))||$bg){$G=($_POST["delete"]?driver()->delete($a,$ko):($_POST["clone"]?queries("INSERT $F$ko".driver()->insertReturning($a)):driver()->update($a,$N,$ko)));$ta=connection()->affected_rows;if(is_object($G))$ta+=$G->num_rows;}else{$Ya=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$jo="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$o);$G=($_POST["delete"]?driver()->delete($a,$jo,1):($_POST["clone"]?queries("INSERT".limit1($a,$F,$jo)):driver()->update($a,$N,$jo,1)));if(!$G)break;$ta+=connection()->affected_rows;}if($Ya&&$G&&!driver()->commit())$G=false;}}$mh=lang(175,$ta);if($_POST["clone"]&&$G&&$ta==1){$zg=last_id($G);if($zg)$mh=lang(197," $zg");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$mh,$G);if($Ya)driver()->rollback();if(!$_POST["delete"]){$Kj=(array)$_POST["fields"];edit_form($a,array_intersect_key($o,$Kj),$Kj,!$_POST["clone"],$m);page_footer();exit;}}elseif(!$_POST["import"]){$G=true;$ta=0;$Ya=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$_n=>$I){$N=array();foreach($I
as$w=>$W){$w=bracket_escape($w,true);$N[idf_escape($w)]=(preg_match('~char|text~',$o[$w]["type"])||$W!=""?adminer()->processInput($o[$w],$W):"NULL");}$G=driver()->update($a,$N," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($_n,true),$o),($bg||$Uj?0:1)," ");if(!$G)break;$ta+=connection()->affected_rows;}if($Ya)$G=$G&&driver()->commit();queries_redirect(remove_from_uri(),lang(175,$ta),$G);if($Ya)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$de=get_file("csv_file",true);if(!is_string($de))$m=upload_error($de);elseif(!preg_match('~~u',$de))$m=lang(285);else{$Hb=array_keys($o);$ll=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$nc=parse_csv($de,$ll);$ta=count($nc);driver()->begin();$J=array();foreach($nc
as$w=>$Y){if(!$w&&!array_diff($Y,$Hb)){$Hb=$Y;$ta--;}else{$N=array();foreach($Y
as$r=>$Db)$N[idf_escape($Hb[$r])]=($Db==""&&$o[$Hb[$r]]["null"]?"NULL":q(csv_value($Db)));$J[]=$N;}}$G=(!$J||driver()->insertUpdate($a,$J,$Uj));if($G)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(286,$ta),$G);driver()->rollback();}}}}$um=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(62).": $um",$m,array(),"",(!$o&&support("table")),($o?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$N=null;if(isset($Jk["insert"])||!support("table")){$N="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$_)?$_[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$N
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$N);if(!$e&&support("table"))echo"<p class='error'>".lang(287)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($L,$e);adminer()->selectSearchPrint($Z,$bl,$v,$R);adminer()->selectOrderPrint($Ki,$Li,$v);adminer()->selectLimitPrint($y);if($Mm!==null)adminer()->selectLengthPrint($Mm);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(120).' '.lang(121)."\n";page_footer();exit;}}$C=$_GET["page"];$we=null;if($C=="last"){$we=get_val(count_rows($a,$Z,$bg,$Me));$C=floor(max(0,intval($we)-1)/$y);}$el=$L;$Ne=$Me;if(!$el){$el[]="*";$ec=convert_fields($e,$o,$L);if($ec)$el[]=substr($ec,2);}foreach($L
as$w=>$W){$n=$o[idf_unescape($W)];if($n&&($Ha=convert_field($n)))$el[$w]="$Ha AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$W){if(isset($el[$w])&&$W["fun"])$el[$w].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$bg&&$Cn){foreach($Cn
as$w=>$W){$el[]=idf_escape($w);if($Ne)$Ne[]=idf_escape($w);}}$G=driver()->select($a,$el,$Z,$Ne,$Ki,$y,$C,true);if(!is_object($G))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$C)$G->seek($y*$C);$rd=array();$J=array();while($I=$G->fetch_assoc()){if($C&&JUSH=="oracle")unset($I["RNUM"]);$J[]=$I;}$Ze=($y&&(support("cursor")?$_GET["next"]!="":count($J)>=$y));if(is_ajax()&&$Ze)header("X-Next-Page: ".pagination_href($C+1));if($_GET["modify"]&&$J){$dh=max_input_vars(count($J[0])+1,20);echo($dh&&count($J)>$dh?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($In).">\n";if($_GET["page"]!="last"&&$y&&$Me&&$bg&&JUSH=="sql")$we=get_val(" SELECT FOUND_ROWS()");if(!$J)echo"<p class='message'>".lang(16)."\n";else{$Ua=adminer()->backwardKeys($a,$um);$Fk=array();reset($L);foreach($J[0]as$w=>$W){if(!isset($Cn[$w])){$W=idx($_GET["columns"],key($L))?:array();$Fk[$w]=array("fun"=>$W["fun"],"col"=>($L?$W["col"]:$w));next($L);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$Me&&$L?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(288)."'".on('click','formCheck','^check').">");$Nh=array();$ok=1;foreach($Fk
as$w=>$W){$n=$o[$W["col"]];$A=($n?adminer()->fieldName($n,$ok):($W["fun"]?($W["col"]!=""?h($W["col"]):"*"):h($w)));if($A!=""){$ok++;$Nh[$w]=$A;$d=idf_escape($w);$pf=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$Ic="&desc[0]=1";$Il=preg_replace('~ DESC( NULLS LAST)?$~','',$Ki[0]);$Kl=($Il==$d||$Il==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($Kl?" aria-sort='".($Il==$Ki[0]?"ascending":"descending")."'":"").">";$Ee=apply_sql_function(h($W["fun"]),$A);$Jl=isset($n["privileges"]["order"])||$W["fun"];echo($Jl?"<a href='".h($pf.($Kl&&$Il==$Ki[0]?$Ic:''))."'>$Ee</a>":$Ee),doc_link(array(JUSH=>$n["doc"]));$lh=($Jl?"<a href='".h($pf.$Ic)."' title='".lang(68)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($n["privileges"]["where"]))$lh
.="<a href='#fieldset-search' title='".lang(65)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($lh?"<span class='column'>$lh</span>":"");}}$Fg=array();if($_GET["modify"]){foreach($J
as$I){foreach($I
as$w=>$W)$Fg[$w]=max($Fg[$w],min(40,utf8_length((string)$W)));}}$kf=array();$jf=array();foreach((array)$_GET["where"]as$W){$W+=array("col"=>"","op"=>"","val"=>"");$Db=$W["col"];$al=$W["val"];if(!is_array($al)&&($al!=""||preg_match('~NULL$~',$W["op"]))&&(!$W["op"]||in_array($W["op"],adminer()->operators($R)))){$Hg=strtr(preg_quote($al),array("%"=>".*?","_"=>"."));$_j=array("LIKE %%"=>$Hg,"ILIKE %%"=>$Hg,"REGEXP"=>$al)+(JUSH=="pgsql"?array("~"=>$al,"~*"=>$al):array())+($Db!=""?array():array("="=>'^'.preg_quote($al).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$al)))).')\z',"LIKE"=>"^$Hg\\z","ILIKE"=>"^$Hg\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($al).'(?=,|\z)',));foreach(($Db!=""?array($Db=>$o[$Db]):$o)as$A=>$n){if($Db!=""||is_searchable($n,$W)){$_i=$W["op"]?:(!preg_match('~'.text_type().'~',$n["type"])?"IN":(preg_match('~%~',$al)?"LIKE":"LIKE %%"));if(isset($_j[$_i])){$yb=preg_match('~^ILIKE|\*$~',$_i)||($_i!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$kf[$A][]="(?".($yb?"i":"").":$_j[$_i])";}elseif($_i=="IS NULL"&&$Db=="")$jf[$A]=true;}}}}echo($Ua?"<th>".lang(289):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($J,$te)as$Hh=>$I){$zn=unique_array($J[$Hh],$v);if(!$zn){$zn=array();foreach($J[$Hh]as$w=>$W){if(!in_array(idx(idx($Fk,$w,array()),"fun"),driver()->grouping))$zn[$w]=$W;}}$_n="";$r=0;foreach($zn
as$w=>$W){$Ek=idx($Fk,$w,array());if($L&&$Ek&&!isset($o[$Ek["col"]]))continue;$Ee=idx($Ek,"fun","");$Db=($Ee?$Ek["col"]:$w);$n=(array)$o[$Db];$ag=is_blob($n);if(!$Ee&&strlen($W)>64&&driver()->md5(idf_escape($Db),$n)){$Ee="md5";$W=md5($ag?(string)driver()->value($W,$n):$W);}if($Ee){$_n
.="&fun[$r]=".url_escape($Ee)."&col[$r]=".url_escape($Db).($W!==null?"&val[$r]=".url_escape($W===false?"f":$W):"");$r++;}else$_n
.="&".($W!==null?"where[".url_escape(bracket_escape($Db))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($Db));}echo"<tr>".(!$Me&&$L?"":"<td class='hover check sticky'>".($bg||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$_n)."' class='edit'>".lang(290)."</a> ").checkbox("check[]",substr($_n,1),in_array(substr($_n,1),(array)$_POST["check"])));foreach($I
as$w=>$W){if(isset($Nh[$w])){$Ee=$Fk[$w]["fun"];$Db=$Fk[$w]["col"];$n=(array)$o[$w];if($W!=""&&(!isset($rd[$w])||$rd[$w]!=""))$rd[$w]=(is_mail($W)?$Nh[$w]:"");$z="";if(is_blob($n)&&$W!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$_n;if(!$z&&$W!==null){foreach((array)$te[$w]as$q){if(count($te[$w])==1||end($q["source"])==$w){$z="";foreach($q["source"]as$r=>$Ll)$z
.=where_link($r,$q["target"][$r],$J[$Hh][$Ll]);$z=($q["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($q["db"]),ME):ME).'select='.url_escape($q["table"]).$z;if($q["ns"]!="")$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($q["ns"]),$z);if(count($q["source"])==1)break;}}}if($Ee=="count"&&$Db==""){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$zn))$z
.=where_link($r++,$V["col"],$V["val"],$V["op"]);}foreach($zn
as$kg=>$V){if(idx(idx($Fk,$kg,array()),"fun")){$z="";break;}$z
.=where_link($r++,$kg,$V);}}$qf=select_value($W,$z,$n,$Mm,($Ee?array():idx($kf,$w,array())));if($W===null&&!$Ee&&isset($jf[$w]))$qf="<mark>$qf</mark>";$t=bracket_escape($_n);$s=h("val[$t][".bracket_escape($w)."]");$Mj=idx(idx($_POST["val"],$t),bracket_escape($w));$Fn=idx($n["privileges"],"update")&&!is_identity_always($n);$md=$J[$Hh][$w];$nd=!is_array($I[$w])&&!is_blob($n)&&is_utf8($md)&&!$Ee&&!$n["generated"]&&$Fn;$T=($Ee=="min"||$Ee=="max"?$o[$Db]["type"]:$n["type"]);$Lm=preg_match('~text|json|lob~',$T);$cg=preg_match(number_type(),$T)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Ee);echo"<td id='$s'".($cg&&($W===null||is_numeric(strip_tags($qf))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$nd&&$md!==null)||$Mj!==null){$Ue=h($Mj!==null?$Mj:$md);echo">".($Lm?"<textarea name='$s' cols='30' rows='".(substr_count($md,"\n")+1)."'>\n$Ue</textarea>":"<input name='$s' value='$Ue' size='$Fg[$w]'>");}else{$Qg=strpos($qf,"<i>…</i>");$ld=adminer()->editVal($md,$n);echo($Fn?" data-text='".($Qg?2:($Lm?1:0))."'".($nd?"":" data-warning='".lang(291)."'").($nd&&!$Qg&&html_entity_decode(strip_tags($qf),ENT_QUOTES,"UTF-8")!==$ld?" data-value='".h($ld)."'":""):"").">$qf";}}}if($Ua)echo"<td>";adminer()->backwardKeysPrint($Ua,$J[$Hh]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$sa=get_settings("adminer_import");if($J||$C||$Ze){$Jd=true;if($_GET["page"]!="last"){if(!$y||(count($J)<$y&&($J||!$C)))$we=($C?$C*$y:0)+count($J);elseif(JUSH!="sql"||!$bg){$we=($bg?null:found_rows($R,$Z));$Jd=!driver()->hasEstimatedRows();if($we===null||(!$Jd&&$we<max(1e4,2*($C+1)*$y))){$we=first(slow_query(count_rows($a,$Z,$bg,$Me)));$Jd=true;}}}if(!support("cursor"))$Ze=(($we===false?count($J)+1:$we-$C*$y)>$y);$dj=($y&&($Ze||$C));if($dj)echo($Ze?'<p><a href="'.h(pagination_href($C+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(292)).'>'.lang(293).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($dj){$bh=($we===false?$C+($J?(count($J)>=$y?2:1):0):floor(($we-1)/$y));echo"<fieldset><legend>".lang(294)."</legend>";if(!support("cursor")){echo
pagination(0,$C).($C>5?" …":"");for($r=max(1,$C-4);$r<min($bh,$C+5);$r++)echo
pagination($r,$C);if($bh>0)echo($C+5<$bh?" …":""),($Jd&&$we!==false?pagination($bh,$C):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$bh'>".lang(295)."</a>");}else
echo
pagination(0,$C).($C>1?" …":""),($C?pagination($C,$C):""),($Ze?pagination($C+1,$C)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(296)."</legend>";$Qc=($Jd?"":"~ ").$we;$sg=($we!==false?($Jd?"":"~ ").lang(179,$we):"");echo
checkbox("all",1,0,$sg,on('click','countRows',$Qc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(180)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(297),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(149),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(282),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$ue=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]||($d["col"]!=""&&!isset($o[$d["col"]]))){unset($ue['sql']);break;}}if($ue){print_fieldset("export",lang(85)." <span id='selected2'></span>");$Zi=adminer()->dumpOutput();echo($Zi?html_select("output",$Zi,$sa["output"])." ":""),html_select("format",$ue,$sa["format"])," <input type='submit' name='export' value='".lang(85)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($rd,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(84)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($In?input_hidden(ini_get("session.upload_progress.name"),$In):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$sa["format"])." <input type='submit' name='import' value='".lang(84)."'>".($In?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$Me&&$L?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$O=isset($_GET["status"]);page_header($O?lang(141):lang(140));$Zn=($O?adminer()->showStatus():adminer()->showVariables());if(!$Zn)echo"<p class='message'>".lang(16)."\n";else{echo"<table>\n";foreach($Zn
as$I){echo"<tr>";$w=array_shift($I);echo"<th><code class='jush-".JUSH.($O?"status":"set")."'>".h($w)."</code>";foreach($I
as$W)echo"<td>".nl_br(h($W));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$im=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$A=>$R){json_row("Comment-$A",h($R["Comment"]).($R["Error"]?" <span class='error'>".h($R["Error"])."</span>":""));if(!is_view($R)||preg_match('~materialized~i',$R["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$A",h($R[$w]));foreach(array_keys($im+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$R))json_row("$w-$A",format_status($R,$w));if($R[$w]!=""&&isset($im[$w]))$im[$w]+=($R["Engine"]!="InnoDB"||$w!="Data_free"?$R[$w]:0);}}}if(function_exists('Adminer\db_status'))$im=db_status();foreach($im
as$w=>$W)json_row("sum-$w",adminer()->formatSizeValue($W));json_row("");}elseif($_GET["script"]=="kill"){if(!$m)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$k=>$W){json_row("tables-$k",format_number($W));json_row("size-$k",db_size($k));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$S=tables_list();if($S)redirect(ME.(support("table")?"table=":"select=").url_escape(key($S)));}$ih=ME.(isset($_GET["select"])?"select=&":"");$Dm=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Dm&&!$m&&!$_POST["search"]){$G=true;$mh="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$G=truncate_tables($_POST["tables"]);$mh=lang(298);}elseif($_POST["move"]){$G=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mh=lang(299);}elseif($_POST["copy"]){$G=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mh=lang(300);}elseif($_POST["drop"]){if($_POST["views"])$G=drop_views($_POST["views"]);if($G&&$_POST["tables"])$G=drop_tables($_POST["tables"]);$mh=lang(301);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("PRAGMA integrity_check(".q($Q).")")as$I)$mh
.="<b>".h($Q)."</b>: ".h($I["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("DBCC CHECKTABLE (".q(table($Q)).") WITH TABLERESULTS")as$I)$mh
.="<b>".h($Q)."</b>: ".h($I["MessageText"])."<br>";}}elseif(JUSH!="sql"){$G=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$mh=lang(302);}elseif(!$_POST["tables"])$mh=lang(13);elseif($G=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($I=$G->fetch_assoc())$mh
.="<b>".h($I["Table"])."</b>: ".h($I["Msg_text"])."<br>";}queries_redirect(relative_uri(),$mh,$G);}page_header(($_GET["ns"]==""?lang(44).": ".h(DB):lang(88).": ".h($_GET["ns"])),$m,true);if(adminer()->homepage()){if($_GET["ns"]!==""){if(support("scheme")&&support("comment")){$f=schema_comment($_GET["ns"]);if($f!="")echo"<p class='nowrap'>".lang(59).": ".adminer()->commentValue('SCHEMA',$f)."\n";}$Ki=$_GET["order"];$Be=($Ki||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(303)."</h3>\n";$Cm=($Be?table_status():tables_list());if(!$Cm)echo"<p class='message'>".lang(13)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(304)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(65)."'>\n","</div></fieldset>\n";if(!$m&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(173).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$Ki&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($ih,0,-1)).'">'.lang(154).'</a>';$e=array("Engine"=>array(lang(305).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$e["Collation"]=array(lang(145).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$e["Data_length"]=array(lang(306).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'refrn/ALL_TABLES.html')),"create",lang(53),);if(support("indexes"))$e["Index_length"]=array(lang(307).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),"indexes",lang(157),);$e["Data_free"]=array(lang(308).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(54));if(function_exists('Adminer\alter_table'))$e["Auto_increment"]=array(lang(60).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(53),);$e["Rows"]=array(lang(309).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'refrn/ALL_TABLES.html')),"select",lang(50),);if(support("comment"))$e["Comment"]=array(lang(59).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE','cockroach'=>'comment-on')),);$Ia=array('Engine','Collation','Comment');foreach($e
as$w=>$d)echo"<th".($Ki==$w?" aria-sort='".(in_array($w,$Ia)?"ascending":"descending")."'":"")."><a href='".h($ih)."order=$w'>$d[0]</a>";echo"<tbody>\n";if($Ki){uasort($Cm,function($ia,$Ra)use($Ki,$Ia){$H=($ia[$Ki]<$Ra[$Ki]?-1:($ia[$Ki]>$Ra[$Ki]?1:0));return(in_array($Ki,$Ia)?$H:-$H);});}$S=0;$im=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Cm
as$A=>$O){$co=($Be?is_view($O):$O!==null&&!preg_match('~table|sequence~i',$O));$O=($Be?$O:array('Engine'=>$O));$s=h("Table-".$A);echo'<tr><td class="hover">'.checkbox(($co?"views[]":"tables[]"),$A,in_array("$A",$Dm,true),"","","",$s),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($A)."' title='".lang(51)."' id='$s'>".h($A).'</a>':h($A));if($co&&!preg_match('~materialized~i',$O['Engine'])){$Rm=lang(153);echo'<td colspan="'.(count($e)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($A)."' title='".lang(52)."'>$Rm</a>":$Rm),"<td align='right'><a href='".h(ME)."select=".url_escape($A)."' title='".lang(50)."'>?</a>";if(support("comment"))echo'<td>'.h($O['Comment']);}else{if($Be){foreach(array_keys($im)as$w)$im[$w]+=($O["Engine"]!="InnoDB"||$w!="Data_free"?idx($O,$w):0);}foreach($e
as$w=>$d){$s=" id='$w-".h($A)."'";echo($d[1]?"<td align='right'>".($w=="Index_length"&&$O["invalid_index"]?"<span class='error'>INVALID</span> ":"")."<a href='".h(ME."$d[1]=").url_escape($A)."'$s title='$d[2]'>".format_status($O,$w)."</a>":"<td$s>".h(idx($O,$w,'?')).($w=="Comment"&&$O["Error"]?" <span class='error'>".h($O["Error"])."</span>":""));}$S++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(283,count($Cm)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Be&&function_exists('Adminer\db_status'))$im=db_status();foreach($im
as$w=>$hm)echo($e[$w]?"<td align='right' id='sum-$w'>".($Be?adminer()->formatSizeValue($hm):""):"");echo"\n","</table>\n",($Be?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Vn="<input type='submit' value='".lang(310)."'".on_help("VACUUM")."> ";$Ei="<input type='submit' name='optimize' value='".lang(311)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Wj=(JUSH=="sqlite"?$Vn."<input type='submit' name='check' value='".lang(312)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Vn.$Ei:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(312)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(313)."'".on_help("ANALYZE TABLE")."> ".$Ei."<input type='submit' name='check' value='".lang(312)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(314)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(315)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(150)."'".confirm().on_help("DROP TABLE").">":"");echo($Wj?"<div class='footer'><div>\n<fieldset><legend>".lang(149)." <span id='selected'></span></legend><div>$Wj\n</div></fieldset>\n":"");$j=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($j)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(316)." <span id='selected3'></span></legend><div>";$k=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($j?html_select("target",$j,$k):'<input name="target" value="'.h($k).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(133)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(23)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(317)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$S).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(86)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(237)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(81)."</h3>\n";$Rk=routines();if($Rk){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(214).'<th>'.lang(58).'<th>'.lang(253)."<td class='hover'><tbody>\n";foreach($Rk
as$I){$A=($I["SPECIFIC_NAME"]==$I["ROUTINE_NAME"]?"":"&name=".url_escape($I["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($I["SPECIFIC_NAME"]).$A).'"'.' title="'.($I["DTD_IDENTIFIER"]=="trigger"?lang(164):lang(224)).'">'.h($I["ROUTINE_NAME"]).'</a>','<td>'.h($I["ROUTINE_TYPE"]),'<td>'.h($I["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($I["SPECIFIC_NAME"]).$A).'">'.lang(160)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(252).'</a>':'').'<a href="'.h(ME).'function=">'.lang(251)."</a>\n","</div>\n";}if(support("sequence")){echo"<div>\n","<h3 id='sequences'>".lang(82)."</h3>\n";$pl=get_vals("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." ORDER BY relname");if($pl){echo"<table class='odds'>\n","<thead><tr><th>".lang(214)."<tbody>\n";foreach($pl
as$W)echo"<tr><th><a href='".h(ME)."sequence=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."sequence='>".lang(258)."</a>\n","</div>\n";}if(support("type")){echo"<div>\n","<h3 id='user-types'>".lang(0)."</h3>\n";$Sn=types();if($Sn){echo"<table class='odds'>\n","<thead><tr><th>".lang(214)."<tbody>\n";foreach($Sn
as$W)echo"<tr><th><a href='".h(ME)."type=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."type='>".lang(263)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(83)."</h3>\n";$J=get_rows("SHOW EVENTS");if($J){echo"<table>\n","<thead><tr><th>".lang(214)."<th>".lang(318)."<th>".lang(243)."<th>".lang(244)."<td class='hover'><tbody>\n";foreach($J
as$I)echo"<tr>","<th>".h($I["Name"]),"<td>".($I["Execute at"]?lang(319)."<td>".h($I["Execute at"]):lang(245)." ".h($I["Interval value"])." ".h($I["Interval field"])."<td>".h($I["Starts"])),"<td>".h($I["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($I["Name"]).'">'.lang(160).'</a>';echo"</table>\n";$Gd=get_val("SELECT @@event_scheduler");if($Gd&&$Gd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Gd)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(242)."</a>\n","</div>\n";}}elseif(support("extension")){$Sd=get_rows("SELECT e.extname, e.extversion, n.nspname, obj_description(e.oid, 'pg_extension') AS comment
FROM pg_extension e
JOIN pg_namespace n ON n.oid = e.extnamespace
ORDER BY e.extname");if($Sd){echo"<div>\n","<h3 id='extensions'>".lang(320)."</h3>\n","<table class='odds'>\n","<thead><tr><th>".lang(214)."<th>".lang(321)."<th>".lang(88)."<th>".lang(59)."<tbody>\n";foreach($Sd
as$I)echo"<tr><th><code class='jush-pgsqlext'>".h($I["extname"])."</code>","<td>".h($I["extversion"]),"<td><a href='".h(substr(ME,0,-1).url_escape($I["nspname"]))."'>".h($I["nspname"])."</a>","<td>".h($I["comment"]),"\n";echo"</table>\n","</div>\n";}}}}page_footer();