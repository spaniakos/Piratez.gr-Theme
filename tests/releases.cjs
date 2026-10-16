/* Disposable real WordPress integration. WP_SOURCE and PIRATEZ_ADDONS_SOURCE
 * point to source trees; installs/upgrades only temporary copies. */
const fs=require('fs'),path=require('path'),os=require('os'),assert=require('node:assert/strict');
const {spawn,execFileSync}=require('child_process'); const {chromium}=require('playwright');
const tmp=fs.mkdtempSync(path.join(os.tmpdir(),'piratez-releases-'));
const theme=path.resolve(__dirname,'..'), addons=process.env.PIRATEZ_ADDONS_SOURCE;
if(!process.env.WP_SOURCE||!addons) throw Error('WP_SOURCE and PIRATEZ_ADDONS_SOURCE required');
fs.cpSync(process.env.WP_SOURCE,path.join(tmp,'site'),{recursive:true});
const fixtures=path.join(tmp,'fixtures'); fs.mkdirSync(fixtures);
for(const [slug,source] of [['piratez-cyberpunk',theme],...['piratez-image-optimize','piratez-cookie-consent'].map(s=>[s,path.join(addons,s)])]) {
 for(const future of [false,true]) {
  const root=path.join(tmp,slug+(future?'-future':'-current')); fs.cpSync(source,root,{recursive:true});
  const cfg=JSON.parse(fs.readFileSync(path.join(root,'release-config.json')));
  if(future) {
   for(const file of [cfg.entry,cfg.type==='theme'?'functions.php':cfg.entry,'readme.txt']) {
    const target=path.join(root,file); fs.writeFileSync(target,fs.readFileSync(target,'utf8').replaceAll(cfg.type==='theme'?'1.3.0':'1.1.0',cfg.type==='theme'?'1.3.1':'1.1.1'));
   }
  }
  execFileSync('python3',['tools/build_release.py'],{cwd:root});
  const dest=path.join(fixtures,slug,future?'future':'current'); fs.mkdirSync(dest,{recursive:true});
  for(const file of [slug+'.zip','release.json'])fs.copyFileSync(path.join(root,'dist',file),path.join(dest,file));
  if(!future) execFileSync('python3',['-m','zipfile','-e',path.join(dest,slug+'.zip'),path.join(tmp,'site/wp-content',slug==='piratez-cyberpunk'?'themes':'plugins')]);
 }
}
fs.mkdirSync(path.join(tmp,'site/wp-content/mu-plugins'),{recursive:true});
fs.writeFileSync(path.join(tmp,'site/wp-content/mu-plugins/release-fixture.php'),`<?php
register_shutdown_function(function(){ $e=error_get_last(); if($e && in_array($e['type'],array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR),true)) echo '<pre>'.json_encode($e).'</pre>'; });
add_filter('pre_http_request', function($pre,$args,$url) {
 $repos=array('spaniakos/Piratez.gr-Theme'=>'piratez-cyberpunk','spaniakos/piratez-image-optimize'=>'piratez-image-optimize','spaniakos/piratez-cookie-consent'=>'piratez-cookie-consent');
 foreach($repos as $repo=>$slug) {
  if(strpos($url,'https://api.github.com/repos/'.$repo.'/')!==0 && strpos($url,'https://github.com/'.$repo.'/releases/')!==0)continue;
  $mode=get_option('fixture_mode','current'); $scenario=get_option('fixture_scenario','');
  $root='/fixtures/'.$slug.'/'.$mode.'/'; $data=json_decode(file_get_contents($root.'release.json'),true); $tag='v'.$data['version'];
  $base='https://github.com/'.$repo.'/releases/download/'.$tag.'/';
  $release=array('tag_name'=>$tag,'draft'=>false,'prerelease'=>$scenario==='prerelease','body'=>'Fixture notes','assets'=>array(array('name'=>$slug.'.zip','browser_download_url'=>$base.$slug.'.zip','state'=>'uploaded','digest'=>'sha256:'.$data['sha256']),array('name'=>'release.json','browser_download_url'=>$base.'release.json','state'=>'uploaded')));
  if($scenario==='origin') $release['assets'][0]['browser_download_url']='https://example.com/evil.zip';
  if($scenario==='digest') $release['assets'][0]['digest']='sha256:'.str_repeat('0',64);
  if($scenario==='incompatible') $data['requires']='99.0';
  if($scenario==='identity') $data['slug']='evil';
  if($scenario==='malformed') $release['tag_name']=array('bad');
  if(str_ends_with($url,'/latest')) $body=wp_json_encode($release);
  elseif(str_ends_with($url,'/release.json')) $body=wp_json_encode($data);
  elseif(str_ends_with($url,'.zip')) { $body=file_get_contents($root.$slug.'.zip'); if($scenario==='tamper') $body.='bad'; }
  else return new WP_Error('fixture_url','Unexpected fixture URL');
  if(!empty($args['stream'])) { file_put_contents($args['filename'],$body); $body=''; }
  return array('headers'=>array('content-type'=>'application/octet-stream'),'body'=>$body,'response'=>array('code'=>200,'message'=>'OK'),'cookies'=>array());
 }
 if(strpos($url,'api.wordpress.org')!==false) return array('headers'=>array(),'body'=>wp_json_encode(array('offers'=>array(),'plugins'=>array(),'themes'=>array(),'no_update'=>array(),'translations'=>array())),'response'=>array('code'=>200,'message'=>'OK'),'cookies'=>array());
 return $pre;
},10,3);
`);
const endpoint=`<?php require __DIR__.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
header('Content-Type: application/json');
$action=$_GET['action']??'';
if($action==='fixture') { update_option('fixture_mode',$_GET['mode']??'current');update_option('fixture_scenario',$_GET['scenario']??''); foreach(array('spaniakos/Piratez.gr-Theme','spaniakos/piratez-image-optimize','spaniakos/piratez-cookie-consent') as $repo)delete_site_transient('piratez_release_'.md5($repo)); }
if($action==='state') {wp_clean_plugins_cache();wp_clean_themes_cache(); echo wp_json_encode(array('theme'=>wp_get_theme()->get('Version'),'plugins'=>get_plugins(),'active'=>get_option('active_plugins'),'keep'=>get_option('fixture_keep'),'theme_keep'=>get_theme_mod('fixture_keep')));exit;}
if($action==='release') {$r=piratez_theme_updater()->release(true); echo wp_json_encode(is_wp_error($r)?array('error'=>$r->get_error_code()):array_merge($r,array('compatible'=>piratez_theme_updater()->compatible($r))));exit;}
if($action==='native') {delete_site_transient('update_plugins');delete_site_transient('update_themes');wp_update_plugins();wp_update_themes();echo wp_json_encode(array('plugins'=>get_site_transient('update_plugins'),'themes'=>get_site_transient('update_themes')));exit;}
if($action==='rename-plugin') {deactivate_plugins('piratez-image-optimize/piratez-image-optimize.php');rename(WP_PLUGIN_DIR.'/piratez-image-optimize',WP_PLUGIN_DIR.'/image-optimize-custom');wp_clean_plugins_cache();}
if($action==='upgrade') {require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';$skin=new WP_Ajax_Upgrader_Skin();if($_GET['id']==='theme') {$upgrader=new Theme_Upgrader($skin);$result=$upgrader->upgrade(get_template());}else{delete_site_transient('update_plugins');wp_update_plugins();$upgrader=new Plugin_Upgrader($skin);$result=$upgrader->bulk_upgrade(array($_GET['id']));}echo wp_json_encode(array('result'=>$result,'errors'=>$skin->get_errors()->get_error_messages()));exit;}
if($action==='activate-image') {activate_plugin('image-optimize-custom/piratez-image-optimize.php');}
if($action==='keep') {update_option('fixture_keep','preserved');set_theme_mod('fixture_keep','preserved');}
if($action==='switch') {switch_theme('twentytwentyfive');}
if($action==='plugin-native') {delete_site_transient('update_plugins');wp_update_plugins();echo wp_json_encode(get_site_transient('update_plugins'));exit;}
if($action==='regressions') {
 $read=piratez_calculate_reading_time(str_repeat('Ελληνική λέξη ',201));
 $a=wp_insert_post(array('post_title'=>'Fixture public','post_status'=>'publish')); $b=wp_insert_post(array('post_title'=>'Fixture related','post_status'=>'publish'));
 $before=piratez_get_related_posts($a);$ids=$before?wp_list_pluck($before->posts,'ID'):array(); wp_update_post(array('ID'=>$b,'post_status'=>'private')); $after=piratez_get_related_posts($a);$next=$after?wp_list_pluck($after->posts,'ID'):array();
 piratez_ai_index_regenerate();update_option('fixture_post',$a);wp_update_post(array('ID'=>$a,'post_title'=>'Changed fixture public'));
 echo wp_json_encode(array('reading'=>$read,'empty'=>piratez_calculate_reading_time(''),'before'=>$ids,'after'=>$next,'related'=>$b));exit;
}
if($action==='static-state') {echo wp_json_encode(array('llms'=>file_get_contents(ABSPATH.'llms.txt'),'xml'=>file_get_contents(ABSPATH.'sitemap.xml')));exit;}
if($action==='private') {wp_update_post(array('ID'=>get_option('fixture_post'),'post_status'=>'private'));}
if($action==='visibility') {update_option('blog_public','0');}
if($action==='setup-front') {set_theme_mod('piratez_dark_mode_default','light');update_option('sidebars_widgets',array('wp_inactive_widgets'=>array(),'footer-1'=>array(),'footer-2'=>array(),'footer-3'=>array()));echo wp_json_encode(array('post'=>wp_insert_post(array('post_title'=>'Browser reliability','post_status'=>'publish','post_content'=>'<p><a href="#1:section">Jump</a></p><h2 id="1:section">Special heading</h2>'.str_repeat('<p>Ελληνικό κείμενο long content.</p>',100)))));exit;}
if($action==='checksum') {require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php'; $client=piratez_theme_updater();$release=$client->release(true);$r=$client->download(false,$release['package'],new Theme_Upgrader(new WP_Ajax_Upgrader_Skin()),array('theme'=>get_template()));echo wp_json_encode(is_wp_error($r)?array('error'=>$r->get_error_code()):array('file'=>$r));exit;}
if($action==='source-identity') {require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';require_once ABSPATH.'wp-admin/includes/file.php';WP_Filesystem();$client=piratez_theme_updater();$release=$client->release(true);$upgrader=new Theme_Upgrader(new WP_Ajax_Upgrader_Skin());$file=$client->download(false,$release['package'],$upgrader,array());$source=get_temp_dir().'identity-check/piratez-cyberpunk/';wp_mkdir_p($source);file_put_contents($source.'style.css',"/* Version: 0.0.0\nUpdate URI: https://github.com/spaniakos/Piratez.gr-Theme\n*/");$r=$client->source($source,dirname($source),$upgrader,array());if(is_string($file))unlink($file);echo wp_json_encode(array('error'=>is_wp_error($r)?$r->get_error_code():'none'));exit;}
if($action==='denied-download') {require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';$r=piratez_theme_updater()->download(false,'https://example.com/evil.zip',new Theme_Upgrader(new WP_Ajax_Upgrader_Skin()),array('theme'=>get_template()));echo wp_json_encode(array('error'=>$r->get_error_code()));exit;}
echo '{}';`;
fs.writeFileSync(path.join(tmp,'site/release-test.php'),endpoint);
const blueprint={steps:[{step:'defineWpConfigConsts',consts:{WP_DEBUG:true,WP_DEBUG_DISPLAY:true,WP_DISABLE_FATAL_ERROR_HANDLER:true}},{step:'runPHP',code:"<?php require '/wordpress/wp-load.php';switch_theme('piratez-cyberpunk');require_once ABSPATH.'wp-admin/includes/plugin.php';activate_plugin('piratez-image-optimize/piratez-image-optimize.php');activate_plugin('piratez-cookie-consent/piratez-cookie-consent.php');"}]};
fs.writeFileSync(path.join(tmp,'blueprint.json'),JSON.stringify(blueprint));
const cli=path.join(path.dirname(require.resolve('@wp-playground/cli/package.json')),'wp-playground.js');
(async()=>{
 const server=spawn('node',[cli,'server','--php=8.3','--wordpress-install-mode=install-from-existing-files','--mount-before-install',path.join(tmp,'site')+':/wordpress','--mount',fixtures+':/fixtures','--blueprint',path.join(tmp,'blueprint.json'),'--login','--port=9402','--workers=1']);
 let logs=''; server.stdout.on('data',d=>logs+=d); server.stderr.on('data',d=>logs+=d);let browser;
 try {
  await new Promise((res,rej)=>{const until=Date.now()+45000,t=setInterval(()=>{if(logs.includes('Ready!')){clearInterval(t);res();}else if(Date.now()>until){clearInterval(t);rej(Error(logs));}},100);server.once('exit',()=>{clearInterval(t);rej(Error(logs));});});
  browser=await chromium.launch({headless:true,executablePath:process.env.PIRATEZ_CHROMIUM_EXECUTABLE,args:['--no-sandbox','--disable-dev-shm-usage']});
  const context=await browser.newContext(),page=await context.newPage(),base='http://127.0.0.1:9402';
  const request=async params=>{const r=await context.request.get(base+'/release-test.php?'+new URLSearchParams(params));const body=await r.text();try{return JSON.parse(body);}catch{throw Error(body.slice(0,4000));}};
  await page.goto(base+'/wp-admin/themes.php?page=piratez-manager-setup');
  if(page.url().includes('wp-login.php')) {await page.locator('#user_login').fill('admin');await page.locator('#user_pass').fill('password');await Promise.all([page.waitForURL(u=>!u.href.includes('wp-login.php')),page.locator('#wp-submit').click()]);}
  assert.equal(await page.getByRole('button',{name:'Install PirateZ Manager',exact:true}).count(),1);
  await request({action:'rename-plugin'});await request({action:'activate-image'});await request({action:'keep'});await request({action:'fixture',mode:'future'});
  let native=await request({action:'native'});assert.equal(native.themes.response['piratez-cyberpunk'].new_version??native.themes.response['piratez-cyberpunk'].version,'1.3.1');
  for(const slug of ['piratez-image-optimize','piratez-cookie-consent']){assert.equal(native.plugins.response[(slug==='piratez-image-optimize'?'image-optimize-custom':slug)+'/'+slug+'.php'].new_version,'1.1.1');const upgraded=await request({action:'upgrade',id:(slug==='piratez-image-optimize'?'image-optimize-custom':slug)+'/'+slug+'.php'});assert.deepEqual(upgraded.errors,[]);}
  assert.deepEqual((await request({action:'upgrade',id:'theme'})).errors,[]);let state=await request({action:'state'});assert.equal(state.theme,'1.3.1');assert.ok(!state.plugins['piratez-manager/piratez-manager.php']);assert.ok(state.active.includes('image-optimize-custom/piratez-image-optimize.php'));assert.ok(state.active.includes('piratez-cookie-consent/piratez-cookie-consent.php'));assert.equal(state.keep,'preserved');assert.equal(state.theme_keep,'preserved');
  for(const slug of ['piratez-image-optimize','piratez-cookie-consent'])assert.equal(state.plugins[(slug==='piratez-image-optimize'?'image-optimize-custom':slug)+'/'+slug+'.php'].Version,'1.1.1');
  for(const [scenario,code] of [['prerelease','piratez_release_version'],['origin','piratez_release_assets'],['digest','piratez_release_digest'],['identity','piratez_release_manifest'],['malformed','piratez_release_version']]) {await request({action:'fixture',mode:'future',scenario});assert.equal((await request({action:'release',id:'theme'})).error,code);}
  await request({action:'fixture',mode:'future',scenario:'incompatible'});assert.equal((await request({action:'release',id:'theme'})).compatible,false);
  await request({action:'fixture',mode:'future',scenario:'tamper'});
  assert.equal((await request({action:'checksum'})).error,'piratez_package_checksum');
  // Administrative actions also require valid per-operation nonces.
  const failure=await context.request.post(base+'/wp-admin/admin-post.php',{form:{action:'piratez_manager_setup',operation:'install',_wpnonce:'invalid'}});assert.equal(failure.status(),403);
  assert.equal((await request({action:'denied-download'})).error,'piratez_package_origin');
  await request({action:'fixture',mode:'future'});
  assert.equal((await request({action:'source-identity'})).error,'piratez_package_identity');
  const regression=await request({action:'regressions'});assert.equal(regression.reading,3);assert.equal(regression.empty,0);assert.ok(regression.before.includes(regression.related));assert.ok(!regression.after.includes(regression.related));
  let index=await request({action:'static-state'});assert.ok(index.llms.includes('Changed fixture public'));
  await request({action:'private'});index=await request({action:'static-state'});assert.ok(!index.llms.includes('Changed fixture public'));
  await request({action:'visibility'});index=await request({action:'static-state'});assert.ok(!index.xml.includes('<url>'));
  const front=await request({action:'setup-front'});await page.emulateMedia({colorScheme:'light',reducedMotion:'reduce'});await page.goto(base+'/?p='+front.post);
  assert.equal(await page.locator('footer.site-footer').count(),1);assert.equal(await page.evaluate(()=>localStorage.getItem('piratez-theme')),null);
  await page.emulateMedia({colorScheme:'dark'});await page.waitForFunction(()=>document.documentElement.dataset.theme==='dark');assert.equal(await page.evaluate(()=>localStorage.getItem('piratez-theme')),null);
  await page.locator('.dark-mode-toggle').click();assert.equal(await page.evaluate(()=>localStorage.getItem('piratez-theme')),'light');await page.emulateMedia({colorScheme:'light'});await page.emulateMedia({colorScheme:'dark'});assert.equal(await page.evaluate(()=>document.documentElement.dataset.theme),'light');
  const errors=[];page.on('pageerror',e=>errors.push(e.message));await page.locator('a[href="#1:section"]').click();await page.evaluate(()=>window.scrollTo(0,document.body.scrollHeight));await page.waitForTimeout(300);assert.equal(await page.locator('.reading-progress-bar').getAttribute('aria-valuenow'),'100');assert.deepEqual(errors,[]);
  const blocked=await browser.newContext();await blocked.addInitScript(()=>{Storage.prototype.getItem=function(){throw new DOMException('Blocked','SecurityError');};Storage.prototype.setItem=function(){throw new DOMException('Blocked','SecurityError');};});const blockedPage=await blocked.newPage();const blockedErrors=[];blockedPage.on('pageerror',e=>blockedErrors.push(e.message));await blockedPage.goto(base+'/?p='+front.post);await blockedPage.locator('.dark-mode-toggle').click();assert.equal(await blockedPage.locator('.dark-mode-toggle').getAttribute('aria-pressed'),'true');assert.deepEqual(blockedErrors,[]);await blocked.close();
  await request({action:'switch'});const independent=await request({action:'plugin-native'});assert.ok(independent.no_update['image-optimize-custom/piratez-image-optimize.php']);assert.ok(independent.no_update['piratez-cookie-consent/piratez-cookie-consent.php']);
  console.log('PASS: WordPress 7.1.3 PHP 8.3; installer-only theme screen; native plugin/theme updates without Manager; settings preserved; independent addon updater; metadata/origin/nonce denials; Greek words/cache/static-index regressions; automatic/manual palette and safe anchors.');
 }finally{if(browser)await browser.close();server.kill('SIGTERM');setTimeout(()=>server.kill('SIGKILL'),1000).unref();}
})().catch(e=>{console.error(e);process.exitCode=1;});
