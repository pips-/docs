<?php

use Phinx\Migration\AbstractMigration;

use Pragma\Docs\Models\Document;

class AddUidToDocuments extends AbstractMigration
{
		public function change()
		{
			$strategy = defined('ORM_UID_STRATEGY') && ORM_UID_STRATEGY == 'mysql' ? 'mysql' : 'php';
			$table = $this->table(Document::getTableName());
			$table->addColumn("uid", "string")
					->update();

			$docs = Document::all();
			if(!empty($docs)){
				foreach($docs as $d){
					$path_elems = explode('/', $d->path);
					if(!empty($path_elems)){
						$file = array_pop($path_elems);
						$uid = substr($file, 0, strlen($file) - (strlen($d->extension) + 1) );
						$d->uid = $uid;
						$d->save();
					}
					else{
						continue;
					}
				}
			}
		}
}
