<?php 

require_once 'BasicFunctions.php';

class BasicTest extends PHPUnit_Framework_TestCase
{
    
    public function testArrayReverse(){
        
        $file = file_get_contents("BasicFunctions.php");
        
        preg_match("/sf_array_reverse(.*)sf_evenly_divisble/s", $file, $matches);
        
        if( count($matches) == 0 ){
            $this->assertTrue(false);
            return;
        }

        $usingArrayReverse = strpos($matches[0], "array_reverse");
        $this->assertTrue( !$usingArrayReverse, "You can't use array_reverse to reverse the array.");
        if( $usingArrayReverse !== false ){
            return;
        }        
        
        $arr = self::getRandomArray();
        $reversed = sf_array_reverse( $arr );
        
        $this->assertInternalType("array", $reversed, "The returned array isn't an array.");        
        
        if( is_array($reversed) ){
            $this->assertTrue( array_reverse($arr) == $reversed, "The returned array is not reversed :(" );
        }
        
    }
    
    public function testEvenlyDivisible(){

        $divisor = rand(3, 5);
        $arr = self::getRandomArray();
        
        $evenlyDivisible = array_reduce($arr, function($result, $el) use ($divisor){
            if( $el % $divisor === 0 ){
                $result[] = $el;
            }
            return $result;
        }, array());
        sort($evenlyDivisible);
        
        $testedEvenlyDivisible = sf_evenly_divisble($arr, $divisor);        
        
        $this->assertInternalType("array", $testedEvenlyDivisible, "The returned array isn't an array.");
        if( is_array($testedEvenlyDivisible) ){
            sort($testedEvenlyDivisible);
            $this->assertTrue( $testedEvenlyDivisible == $evenlyDivisible, "The returned array doesn't match expected." );
        }
    }
    
    public function testClosure(){
        $l = rand(1, 100);
        $r = rand(1, 100);
        
        $fn = sf_get_sum_closure($l, $r);
        
        $this->assertTrue( gettype($fn) == "object", "A function was not returned :(");
        if( gettype($fn) == "object" ){
            $this->assertEquals( $fn(), $l + $r);
        }        
    }
    
    public function testExtractLinks(){
        
        $html = <<<EOF
<div class="span7">
          			
          			<div class="row-fluid top-row">
          				
          				<div class="span6">
          					<div class="metro-tile big-tile green">
          					    <div class="tile-inner-image"><a href="/what-we-do/"><img src="/wp-content/themes/setfive_three/images/settings.png"></a></div>
          						<div class="tile-inner"><a href="/what-we-do/">What we do &gt;</a></div>
          					</div>
          				</div>
          				
          				<div class="span6">
          					<div class="metro-tile big-tile purple">
          					    <div class="tile-inner-image"><a href="/about-us/"><img src="/wp-content/themes/setfive_three/images/user.png"></a></div>
          						<div class="tile-inner"><a href="/about-us/">Team &gt;</a></div>
          					</div>
          				</div>
          				
          			</div>          			
          			
          			<div class="row-fluid bottom-row">
          				
          				<div class="span6">
          					<div class="metro-tile big-tile orange">
          					    <div class="tile-inner-image"><a href="/our-stack/"><img src="/wp-content/themes/setfive_three/images/storage.png"></a></div>
          						<div class="tile-inner"><a href="/our-stack/">Our stack &gt;</a></div>
          					</div>
          				</div>
          				
          				<div class="span6">
          					<div class="metro-tile big-tile red">
          					    <div class="tile-inner-image"><a href="/work/"><img src="/wp-content/themes/setfive_three/images/gallery1.png"></a></div>
          						<div class="tile-inner"><a href="/work/">Work &gt;</a></div>
          					</div>
          				</div>
          				
          			</div>      			          			
          			          			
          			<div class="banner"><h3>Get in touch</h3></div>
          			<div class="row-fluid">
          			    <div class="span6">
                                        <form action="/wp-content/themes/setfive_three/sendContactEmail.php" data-provide="contact-form">

<div data-provide="contact-success" class="alert alert-success hide">Your message has been sent!</div>

          			        <ul class="listless contact-form">
          			            <li><input type="text" placeholder="Enter your email address..." class="input-block-level" name="contact[email]"></li>
          			            <li><textarea placeholder="Enter your message..." class="input-block-level" name="contact[message]"></textarea></li>
          			            <li><input type="submit" value="Send Message" class="btn pull-right"></li>
          			        </ul>
                                      <input type="hidden" name="contact[is_human]">
                                      </form>         			        
          			    </div>
          			    <div class="span6">
          			        <ul class="listless contact-form centered">
          			            <li><a target="_blank" href="http://goo.gl/maps/EtCPp"><img src="http://maps.googleapis.com/maps/api/staticmap?center=Central%20Square%20Cambridge,%20MA&amp;zoom=15&amp;size=315x175&amp;maptype=roadmap&amp;sensor=false" class="shadowed-thumbnail"></a></li>
          			            <li class="tight"><a href="mailto:contact@setfive.com">contact@setfive.com</a> | <a href="https://www.twitter.com/setfive">@setfive</a> | <a href="tel:6178630440">617-863-0440</a></li>
          			            <li>678 Massachusetts Ave. Cambridge, MA 02139</li>
          			        </ul>
          			    </div>
          			</div>
          			
          		</div>        
EOF;
        
        $links = sf_extract_links( $html );
        $this->assertInternalType("array", $links, "The returned array isn't an array.");
        
        $target = json_decode('["\/what-we-do\/","\/what-we-do\/","\/about-us\/","\/about-us\/","\/our-stack\/","\/our-stack\/","\/work\/","\/work\/","mailto:contact@setfive.com","https:\/\/www.twitter.com\/setfive","tel:6178630440"]', true);

        if( is_array($links) ){
            sort($links);
            sort($target);
            $this->assertTrue( $links == $target, "The array doesn't contain the links from the HTML!" );
        }
    }
    
    private static function getRandomArray(){
        $arr = array();
        for($i = 0; $i < rand(15, 25); $i++){
            $arr[] = rand(1, 100);
        }
        return $arr;        
    }
}