<?php

use yii\db\Migration;

/**
 * Class m230822_083459_modify_repair_request_table
 */
class m230822_083459_modify_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('repair_request', 'person_trapped');
        $this->dropColumn('repair_request', 'system_operational');
        $this->dropColumn('repair_request', 'schedule');
        $this->dropColumn('repair_request', 'extra_cost');
        $this->dropColumn('repair_request', 'eta');
        $this->dropColumn('repair_request', 'rating');
        $this->dropColumn('repair_request', 'problem_input');
        $this->dropColumn('repair_request', 'rejection_reason');
        $this->dropColumn('repair_request', 'note_client');
        $this->dropColumn('repair_request', 'customer_name');
        $this->dropColumn('repair_request', 'atl_note');
        $this->dropColumn('repair_request', 'confirmed_equipment');
        $this->dropColumn('repair_request', 'missing_signature');
        $this->dropColumn('repair_request', 'pending_equipment_id');
        $this->dropColumn('repair_request', 'last_handled_by');
        $this->dropColumn('repair_request', 'works_completed');
        $this->dropColumn('repair_request', 'hard_copy_report');
        $this->dropColumn('repair_request', 'worker_id');

        $this->dropForeignKey('fk_repair_request_user_id__user_id', 'repair_request');
        $this->dropIndex('fk_repair_request_user_id__user_id_idx', 'repair_request');
        $this->dropColumn('repair_request', 'user_id');

        $this->dropForeignKey('fk_repair_request_gallery', 'repair_request');
        $this->dropIndex('fk_repair_request_gallery_idx', 'repair_request');
        $this->dropColumn('repair_request', 'gallery_id');

        $this->dropForeignKey('fk_repair_request_related_request_id', 'repair_request');
        $this->dropIndex('idx_repair_request_related_request_id', 'repair_request');
        $this->dropColumn('repair_request', 'related_request_id');

        $this->dropForeignKey('fk_repair_request_problem_problem_id', 'repair_request');
        $this->dropIndex('fk_repair_request_problem_problem_id_idx', 'repair_request');
        $this->dropColumn('repair_request', 'problem_id');

        // Add Columns
        $this->addColumn("repair_request", "project_id", $this->integer(11));
        $this->createIndex("fk_project_repair_project_id_idx", "repair_request", "project_id");
        $this->addForeignKey(
            'fk_project_repair_project_id',
            'repair_request',
            'project_id',
            'project',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("repair_request", "location_id", $this->integer(11));
        $this->createIndex("fk_project_repair_location_id_idx", "repair_request", "location_id");
        $this->addForeignKey(
            'fk_project_repair_location_id',
            'repair_request',
            'location_id',
            'location',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->renameColumn('repair_request', 'type', 'service_type');

        $this->addColumn('repair_request', 'repair_request_path', $this->string());
        $this->addColumn('repair_request', 'problem', $this->string());
        $this->addColumn('repair_request', 'need_review', $this->integer());
        $this->addColumn('repair_request', 'technician_from_another_division', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('repair_request', 'person_trapped', $this->tinyInteger(1));
        $this->addColumn('repair_request', 'system_operational', $this->tinyInteger(1));
        $this->addColumn('repair_request', 'schedule', $this->integer(11));
        $this->addColumn('repair_request', 'extra_cost', $this->double());
        $this->addColumn('repair_request', 'eta', $this->dateTime());
        $this->addColumn('repair_request', 'rating', $this->double());
        $this->addColumn('repair_request', 'problem_input', $this->string());
        $this->addColumn('repair_request', 'rejection_reason', $this->string());
        $this->addColumn('repair_request', 'note_client', $this->text());
        $this->addColumn('repair_request', 'customer_name', $this->string());
        $this->addColumn('repair_request', 'atl_note', $this->string());
        $this->addColumn('repair_request', 'confirmed_equipment', $this->tinyInteger(1));
        $this->addColumn('repair_request', 'missing_signature', $this->tinyInteger(1));
        $this->addColumn('repair_request', 'pending_equipment_id', $this->integer(11));
        $this->addColumn('repair_request', 'last_handled_by', $this->integer(11));
        $this->addColumn('repair_request', 'works_completed', $this->tinyInteger(1));
        $this->addColumn('repair_request', 'hard_copy_report', $this->string());
        $this->addColumn('repair_request', 'worker_id', $this->integer(11));

        $this->addColumn("repair_request", "user_id", $this->integer(11));
        $this->createIndex("fk_repair_request_user_id__user_id_idx", "repair_request", "user_id");
        $this->addForeignKey(
            'fk_repair_request_user_id__user_id',
            'repair_request',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("repair_request", "gallery_id", $this->integer(11));
        $this->createIndex("fk_repair_request_gallery_idx", "repair_request", "gallery_id");
        $this->addForeignKey(
            'fk_repair_request_gallery',
            'repair_request',
            'gallery_id',
            'gallery',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("repair_request", "related_request_id", $this->integer(11));
        $this->createIndex("idx_repair_request_related_request_id", "repair_request", "related_request_id");
        $this->addForeignKey(
            'fk_repair_request_related_request_id',
            'repair_request',
            'related_request_id',
            'repair_request',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("repair_request", "problem_id", $this->integer(11));
        $this->createIndex("fk_repair_request_problem_problem_id_idx", "repair_request", "problem_id");
        $this->addForeignKey(
            'fk_repair_request_problem_problem_id',
            'repair_request',
            'problem_id',
            'problem',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // drop Columns

        $this->dropForeignKey('fk_project_repair_project_id', 'repair_request');
        $this->dropIndex('fk_project_repair_project_id_idx', 'repair_request');
        $this->dropColumn('repair_request', 'project_id');

        $this->dropForeignKey('fk_project_repair_location_id', 'repair_request');
        $this->dropIndex('fk_project_repair_location_id_idx', 'repair_request');
        $this->dropColumn('repair_request', 'location_id');

        $this->renameColumn('repair_request', 'service_type', 'type');

        $this->dropColumn('repair_request', 'repair_request_path');
        $this->dropColumn('repair_request', 'problem');
        $this->dropColumn('repair_request', 'need_review');
        $this->dropColumn('repair_request', 'technician_from_another_division');
    }
}
