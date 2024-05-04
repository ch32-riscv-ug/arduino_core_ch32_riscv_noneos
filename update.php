<?php

chdir(dirname(__FILE__));
system('rm -rfv arduino*');

$projects_name = 'arduino_core_ch32_riscv_noneos';
$ver = '1.0.1';

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
    $cmd = "cp -rfv $path/EVT/EXAM/SRC/* $dir/SRC";
    echo("exec $cmd\n");
    system($cmd);

    // S to inc
    $cmd = "rename .S .inc $dir/SRC/Startup/*.S";
    echo("exec $cmd\n");
    system($cmd);

    // copy USER
    $cmd = "cp -rfv $path/EVT/EXAM/GPIO/GPIO_Toggle/User/* $dir/USER";
    echo("exec $cmd\n");
    system($cmd);

    // del main.c
    $cmd = "rm -rfv $dir/USER/main.c";
    echo("exec $cmd\n");
    system($cmd);

    // copy common
    $cmd = "cp -rfv ../../common/* $dir/";
    echo("exec $cmd\n");
    system($cmd);
}

// ch base
chdir('..');

// copy copy
$cmd = "cp -rfv ../copy/* .";
echo("exec $cmd\n");
system($cmd);

// getcwd()
$pwd = getcwd();

// patch
echo "# patch\n";
$it = new RecursiveDirectoryIterator('.');
$it = new RecursiveIteratorIterator(
    $it,
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($it as $file) {
    if(preg_match("/\.patch$/", $file->getFilename())) {
        echo sprintf(
            "%s/%s\n",
            $file->getPath(),
            $file->getFilename()
        );

        chdir($file->getPath());
        system("patch < " . $file->getFilename());
    }
}

chdir(dirname(__FILE__));
system("zip -r $projects_name.$ver.zip $projects_name");
system("ls -la $projects_name.$ver.zip");
system("sha256sum $projects_name.$ver.zip");

