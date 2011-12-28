<?php defined('_JEXEC') or die('Restricted access'); 

JHTML::_('behavior.tooltip');


$document=JFactory::getDocument();
$document->addStyleSheet('components/com_get_soft/assets/css/gallery.css');
$document->addScript('components/com_get_soft/assets/js/gallery.js');

$document->addStyleSheet('components/com_get_soft/assets/slimbox/css/slimbox.css');
$document->addScript('components/com_get_soft/assets/slimbox/js/slimbox.js');

$config=$this->config;
$pageNav=$this->pageNav;
$images=$this->images;

$r=JURI::root().'components/com_get_soft/';

$list			=	$this->list;

?>
<script type="text/javascript">
	var allow_report="<?php echo  $config->allow_report; ?>";
	if(allow_report==1)
	{	
		load_report();
	}
</script>
<div class="gallery">
<?php if ($this->category->description) { ?>
	<div class="cat-info">
		<h1><?php echo $this->category->title; ?></h1>
	</div>
<?php } ?>
	<div class="filter">
		<?php echo $list['color']; ?>
		<?php echo $list['pattern']; ?>
	</div>
<?php
for ($i=0;$i<count($images);$i++)
{			
	$obj_image=$images[$i];				
	?>
	<div class="img_ad_box">
		<div class="img_box">
			<a class="img_small_thumb" rel="lightbox"
					title="<?php echo  htmlspecialchars($obj_image->get('name')); ?>"
					href="<?php echo $obj_image->get_image('link'); ?>">
				<img class="bordered" src="<?php echo $obj_image->get_small_thumb('link'); ?>" alt="<?php echo  htmlspecialchars($obj_image->get('name')); ?>" />	
			</a>
		</div>
		<div class="img_name"><?php echo  $obj_image->get('name'); ?></div>
		<?php 
		if($config->allow_report) 
		{
			?>
			<div class="img_report" style="display: none;"> 
				<a class="" href="<?php echo  $obj_image->get_report_link(); ?>" onclick="return confirm('<?php echo  JText::_('Are you sure?') ?>');">
					<?php echo  JText::_('Report')?>	
				</a>
			</div>
			<?php 
		}
			?>
	</div>
	<?php
}
?>
	<div style="clear:both"></div>
	<br />
<?php
	echo $pageNav->getPagesLinks();
	$limitstart		=	$pageNav->limitstart;
?>
	<input type="hidden" id="limitstart" name="limitstart" value="<?php echo  $limitstart; ?>" />
	<input type="hidden" id="Itemid" name="Itemid" value="<?php echo  JRequest::getVar('Itemid'); ?>" />
	<input type="hidden" id="url" name="url" value="<?php echo  JURI::root().'index.php?option=com_get_soft&view=gallery'; ?>" />
</div>
