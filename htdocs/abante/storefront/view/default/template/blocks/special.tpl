<?php if ( $block_framed ) { ?>
<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/special.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">

<?php
}

if ($products) {
    foreach ($products as $product) {  
        $item['image'] = "<img src='".$product['thumb']['thumb_url']."' width='50' alt='".$product['name']."' />";
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];
        $item['rating'] = ($product['rating']) ? "<img src='".$this->templateResource('/image/stars_'.$product['rating'].'.png')."' alt='".$product['stars']."' />" : '';
        
        if (!$product['special']) {
            $item['price'] = $product['price'];
        } else {
            $item['price'] = "<span>".$product['price']."</span> ".$product['special'];
        }
        
        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
	    if(!$display_price){
		    $item['price'] = '';
	    }
?>
<div class="list_item">
    <div class="rightPane">
        <div class="title"><a href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a></div>
        <div class="description"><?php echo $item['description']?></div>
	    <?php if($item['rating']){?>
        <div class="rating"><?php echo $item['rating']?></div> <?php }?>
        <div class="price-add">
            <span class="price"><?php echo $item['price']?></span>
            <a class="info" href="<?php echo $item['info_url']?>"></a>
            <a class="buy" id="<?php echo $product['product_id']?>" href="<?php echo $item['buy_url']?>"></a>
        </div>
    </div>
    <div class="image"><a href="<?php echo $item['info_url']?>"><?php echo $item['image']?></a></div>
    <div style="clear: both;"></div>
</div>
<?php
    }
}
if ( $block_framed ) {
?>

            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>
<?php } ?>
