<?php

use Phinx\Migration\AbstractMigration;

class CreateVideosTable extends AbstractMigration
{
    public function change()
    {
        $this->table('Videos', [
            'id' => false,
            'primary_key' => 'VideoId'
        ])
            ->addColumn('VideoId', 'string', ['length' => 50])
            ->addColumn('TimeCreated', 'datetime')
            ->addColumn('Username', 'string') // References users
            ->addColumn('Filename', 'string')
            ->addColumn('Thumbnail', 'string')
            ->addColumn('Title', 'string')
            ->addColumn('Description', 'text', ['default' => null])
            ->addColumn('Uploader', 'text', ['default' => null])
            ->addColumn('Source', 'text', ['default' => null])
            ->addColumn('Licence', 'string', ['default' => null])
            ->addColumn('Length', 'integer', ['default' => null])
            ->addColumn('Width', 'integer', ['default' => null])
            ->addColumn('Height', 'integer', ['default' => null])
            ->addColumn('Format', 'string', ['length' => 10, 'default' => null])
            ->addColumn('Bitrate', 'integer', ['default' => null])
            ->addColumn('Size', 'integer', ['default' => null])
            ->addColumn('Views', 'biginteger', ['default' => 0])
            ->addColumn('PartialViews', 'biginteger', ['default' => 0])
            ->addColumn('Bounces', 'biginteger', ['default' => 0])
            ->addColumn('Privacy', 'integer', ['default' => null])
            ->addColumn('Fb_Pos', 'biginteger', ['default' => 0])
            ->addColumn('Fb_Neg', 'biginteger', ['default' => 0])
            ->create();
    }
}
