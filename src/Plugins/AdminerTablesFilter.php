<?php

/** Filter names in tables list
* @link https://www.adminer.org/plugins/#use
* @author Jakub Vrana, https://www.vrana.cz/
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*
*
* @modified by Adre
*/
class AdminerTablesFilter extends Adminer\Plugin {
    public function head()
    {
        $asset = 'assets/js/table-filter.js';
        $version = filemtime(dirname(__DIR__, 2) . '/public/' . $asset);
        echo '<script' . Adminer\nonce() . ' defer src="' . $asset . '?v=' . $version . '"></script>';
    }

    public function tablesPrint($tables)
    {
        echo '<p class="jsonly"><input id="filter-field" autocomplete="off" type="search" placeholder="Find a table" aria-label="Find a table"></p>';
    }

	protected $translations = array(
		'cs' => array('' => 'Filtruje názvy v seznamu tabulek'),
		'de' => array('' => 'Filtern Sie Namen in der Tabellenliste'),
		'pl' => array('' => 'Filtruj nazwy na liście tabel'),
		'ro' => array('' => 'Nume de filtre în lista de tabele'),
		'ja' => array('' => 'テーブル一覧をテーブル名でフィルタリング'),
	);
}
