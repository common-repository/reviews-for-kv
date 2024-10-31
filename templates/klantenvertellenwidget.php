<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="klant-review<?php echo $noborder ?>">
	<div itemscope itemtype="http://schema.org/Review">

		<div itemprop="author" itemscope itemtype="http://schema.org/Person">

			<span itemprop="name" class="name"><?php echo $review['Voornaam:']; ?></span>
			<span class="pull-right" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><meta itemprop="worstRating" content="1"><h3 itemprop="ratingValue" class="score"><?php echo $review['Totaal oordeel']; ?></h3><span itemprop="bestRating" content="10,0"></span></span>
			<div class="clearfix"></div>
			<!-- <br> -->
			<span class="desc" itemprop="reviewBody"><?php echo $review['Ervaring:']; ?></span>
			<br><br>
			<div class="date" ><meta itemprop="datePublished" content="<?php echo $review['datum']; ?>"><?php echo translate_month(Carbon\Carbon::parse($review['datum'])->format('d M Y')); ?></div>
		</div>
	</div>
</div>

