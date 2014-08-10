<?php

/* 
<div class="infoGoodItem-wp-photos_main">
                        <img style="max-width: 310px; height: auto;" src="<?php
                        echo Yii::app()->createUrl("site/download", array(
                            'id'=>$data['images'][0]['file'],
                            'filename'=>$data['images'][0]['link'],
                            'link'=>$data['link'],
                            'language'=>  Language::getCurrentZone(),
                        ));
                        ?>">
                    </div>
                    <div class="infoGoodItem-wp-photos_all">
                        <div class="item_photos_all">
                            <?php foreach($data['images'] as $key=>$image): ?>
                            <div class="slide"><img src="<?php
                            echo Yii::app()->createUrl("site/download", array(
                                'id'=>$image['file'],
                                'filename'=>$data['link']."-".$key.".".$image['ext'],
                                'link'=>$data['link'],
                                'language'=>  Language::getCurrentZone(),
                            ));
                            ?>" alt="<?php echo htmlspecialchars($data['manufacturer']. " " .$data['name'])?>"></div>
                            <?php endforeach;?>
                        </div>
                    </div>
 */

