<?php

/**
 * Este arquivo realiza uma instalação completa do winPHP neste diretorio.
 * Copiando todos os arquivos base de www/ e tambem todos os arquivos dentro de cada modulo em modules/
 */

/**
 * Delete all files/folder recursive
 * @param string dir
 */
function deleter($dir) {
	$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
	$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($ri as $file) {
		$file->isDir() ? rmdir($file) : unlink($file);
	}
	return true;
}

/**
 * Copy a file, or recursively copy a folder and its contents
 * @param string $source
 * @param string $dest
 */
function copyr($source, $dest) {
	if (is_link($source)) {
		return symlink(readlink($source), $dest);
	}

	if (is_file($source)) {
		return copy($source, $dest);
	}

	if (!is_dir($dest)) {
		mkdir($dest);
	}

	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		copyr("$source/$entry", "$dest/$entry");
	}

	// Clean up
	$dir->close();
	return true;
}

/**
 * Chmod recursive
 * @param string $path
 */
function chmodr($path) {
	$dir = new DirectoryIterator($path);
	foreach ($dir as $item) {
		@chmod($item->getPathname(), 0777);
		if ($item->isDir() && !$item->isDot()) {
			chmodr($item->getPathname());
		}
	}
}

/* Create www */
if (!is_dir('www')) {
	mkdir("www");
}


/* Clear www */
deleter("www/");


/* Base */
copyr('../www/', 'www/');


/* Modulos */
$dir = dir('../modules/');
while (false !== $entry = $dir->read()) {
	if ($entry == '.' || $entry == '..' || $entry == 'README.md') {
		continue;
	}
	copyr("../modules/$entry", "www/");
}


/* Chmod */
chmodr('www/');


echo 'Instalação do WinPHP realizada com sucesso! <a target="_blank" href="www/">Abrir winPHP</a>';
