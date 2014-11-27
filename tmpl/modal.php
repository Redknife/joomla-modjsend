<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jsend
 *
 * @copyright   Copyright (C) 2012 Saity 74 LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen');

$list = trim($params->get('list', ''));
$list_arr = explode("\n", $list);

if ($list && !empty($list_arr)){
    $list = array();
    foreach($list_arr as $option){
        $list[] = JHTML::_('select.option', $option , $option );
    }
    $list_html = JHTML::_('select.genericlist', $list, $name = 'list', 'inputbox');
}

?>
<a data-toggle="modal" data-target="#<?php echo $params->get('form_wrapper_id'); ?>" class="modal-btn-jsend <?php echo $params->get('modal_btnclass'); ?>"><?php echo $params->get('modal_btntext'); ?></a>

<div class="modal fade" id="<?php echo $params->get('form_wrapper_id'); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $params->get('form_wrapper_id'); ?>Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
            </div>
            <div class="modal-body">
                <div class="form-wrapper">
                    <div class="form-bg">
                        <form class="mail-form form-validate <?php echo $params->get('form_suffix'); ?>" method="post" action="/index.php" enctype="multipart/form-data">
                            <?php if ($header = $params->get('header')) : ?>
                            <div class="form-header">
                                <h2><?php echo $header ?></h2>
                            </div>
                            <?php endif; ?>
                            <?php if ($text = $params->get('text')) : ?>
                            <div class="form-introtext"><?php echo $text ?></div>
                            <?php endif; ?>

                            <?php if ($showlist = $params->get('showlist', 0)) : ?>
                            <div class="controlls">
                               <?php echo $list_html ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($showname = $params->get('showname', 0)) : ?>
                            <div class="controlls">
                                <input type="text" class="<?php echo $showname == 2 ? 'required' : '' ?> field" <?php if($params->get('showname') == 2) echo 'data-validation="required"'; ?> value="" name="name" placeholder="<?php echo $params->get('name_placeholder'); ?>" />
                            </div>
                            <?php endif; ?>

                            <?php if ($showfirm = $params->get('showfirm', 0)) : ?>
                            <div class="controlls">
                                <input type="text" class="<?php echo $showfirm == 2 ? 'required' : '' ?> field" <?php if($params->get('showfirm') == 2) echo 'data-validation="required"'; ?> value="" name="firm" placeholder="<?php echo $params->get('firm_placeholder'); ?>" />
                            </div>
                            <?php endif; ?>

                            <?php if ($showphone = $params->get('showphone', 0)) : ?>
                            <div class="controlls">
                                <input type="text" class="<?php echo $showphone == 2 ? 'required' : '' ?> field" <?php if($params->get('showphone') == 2) echo 'data-validation="custom" data-validation-regexp="^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$"'; ?> value="" name="phone" placeholder="<?php echo $params->get('phone_placeholder'); ?>" />
                            </div>
                            <?php endif; ?>

                            <?php if ($showemail = $params->get('showemail', 0)) : ?>
                            <div class="controlls">
                                <input type="text" class="<?php echo $showemail == 2 ? 'required' : '' ?> field" <?php if($params->get('showemail') == 2) echo 'data-validation="email"' ; ?> value="" name="email" placeholder="<?php echo $params->get('email_placeholder'); ?>" />
                            </div>
                            <?php endif; ?>

                            <?php if ($showdesc = $params->get('showdesc', 0)) : ?>
                            <div class="controlls">
                                <textarea class="<?php echo $showdesc == 2 ? 'required' : '' ?> field" <?php if($params->get('showdesc') == 2) echo 'data-validation="required"'; ?> name="desc" placeholder="<?php echo $params->get('desc_placeholder'); ?>" ></textarea>
                             </div>
                            <?php endif; ?>

                            <?php if ($showfileupload = $params->get('showfileupload', 0)) : ?>
                            <div class="form-uploadfile">
                                <div class="upload-control">
                                    <div class="uploader-wrap">
                                        <span>У вас уже есть проект или идеи? Прикрепите их к письму</span>
                                        <input class="field input-file" name="Filedata" type="file" id="file"/>
                                    </div>
                                </div>
                                <div id="filename" class="filename-wrapper">
                                    <span class="flname"></span><span id="remove-file" class="remove-file-btn" class="icon-black icon-remove-circle"></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="controlls">
                                <a href="#" class="btn-jsend-submit <?php echo $params->get('btnclass'); ?>"><?php echo $params->get('btntext'); ?></a>
                            </div>

                            <input type="hidden" name="hash" value="<?php echo base64_encode($module->position) ?>" />
                            <?php echo JHtml::_( 'form.token' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>