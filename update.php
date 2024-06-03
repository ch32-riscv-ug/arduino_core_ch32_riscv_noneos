<?php

chdir(dirname(__FILE__));
system('rm -rfv arduino*');

$projects_name = 'arduino_core_ch32_riscv_noneos';
$ver = '1.2';

mkdir($projects_name);
chdir($projects_name);

mkdir('libraries');
mkdir('variants');

mkdir('cores');
chdir('cores');

$dirlist = glob("../../EVT/CH32????");
foreach($dirlist as $path){
    $dir = basename($path);
    echo "========================================\n";
    echo "PATH $path\n";
    echo "DIR $dir\n";

    mkdir($dir);

    mkdir($dir.'/SRC');
    mkdir($dir.'/USER');

    // copy SRC
    $cmd = "cp -rfvp $path/EVT/EXAM/SRC/* $dir/SRC";
    echo("exec $cmd\n");
    system($cmd);

    // S to inc
    $cmd = "rename .S .inc $dir/SRC/Startup/*.S";
    echo("exec $cmd\n");
    system($cmd);

    // copy USER
    $cmd = "cp -rfvp $path/EVT/EXAM/GPIO/GPIO_Toggle/User/* $dir/USER";
    echo("exec $cmd\n");
    system($cmd);

    // del main.c
    $cmd = "rm -rfv $dir/USER/main.c";
    echo("exec $cmd\n");
    system($cmd);

    // copy common
    $cmd = "cp -rfvp ../../common/* $dir/";
    echo("exec $cmd\n");
    system($cmd);
}

// ch base
chdir('..');

// copy copy
$cmd = "cp -rfvp ../copy/* .";
echo("exec $cmd\n");
system($cmd);

// getcwd()
$pwd = getcwd();

// patch
echo "################################################################\n";
echo "# patch\n";
$it = new RecursiveDirectoryIterator('.');
$it = new RecursiveIteratorIterator(
    $it,
    RecursiveIteratorIterator::LEAVES_ONLY
);
$patch_list = array();
foreach ($it as $file) {
    if(preg_match("/\.patch$/", $file->getFilename())) {
        $item = array();
        $item['path'] = $file->getPath();
        $item['file'] = $file->getFilename();
        $patch_list[] = $item;
    }
}

foreach($patch_list as $patch){
    chdir(dirname(__FILE__));
    chdir($projects_name);
    chdir($patch['path']);
    echo $patch['path'] . $patch['file'] . "\n";
    system("patch < " . $patch['file']);
}
echo "################################################################\n";

chdir(dirname(__FILE__));

system('find ' . $projects_name . ' -name "*.patch" | xargs rm');

system("zip -r $projects_name.$ver.zip $projects_name >zip.log");

// JSON
system("ls -la $projects_name.$ver.zip");
$ret = system("sha256sum $projects_name.$ver.zip");
$ret2 = explode(" ", $ret);
$hash = trim($ret2[0]);
echo "hash : '$hash'\n";

$filesize = filesize("$projects_name.$ver.zip");
echo "filesize : $filesize\n";

$jsonlist = glob("*.json");
foreach($jsonlist as $jsonfile){
  echo "json : $jsonfile\n";

  $file = file_get_contents($jsonfile);
  $data = json_decode($file, true);
  $data['packages'][0]['platforms'][0]['checksum'] = "SHA-256:$hash";
  $data['packages'][0]['platforms'][0]['size'] = $filesize;

  $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  file_put_contents($jsonfile, $json);
}
