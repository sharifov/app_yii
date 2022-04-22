<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 */
?>
<div class="row">
    <div class="col-md-8 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading"><?= Yii::t('admin/mailer', 'Available variables') ?></div>
            <div class="panel-body">
                <p><?= Yii::t('admin/mailer', 'Following variables could be used in the mail body and subject in form <code>{variable_name}</code>') ?></p>
                <table class="table">
                    <tr>
                        <th><?= Yii::t('admin/mailer', 'Variable') ?></th>
                        <th><?= Yii::t('admin/mailer', 'Description') ?></th>
                    </tr>
                    <tr>
                        <td>
                            <code>{email}</code>
                        </td>
                        <td>
                            <?= Yii::t('admin/mailer', 'Email of the current recipient of the letter') ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code>{firstName}</code>
                        </td>
                        <td>
                            Имя участника
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code>{lastName}</code>
                        </td>
                        <td>
                            Фамилия участника
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>