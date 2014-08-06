<h1 class="title">Поиск</h1>

                <div class="bl_sort clr">
                    <span class="bl_sort-t">Сортировка:</span>
                    <span class="bl_sort-link"><a class="active" href="#">по рейтингу</a></span>
                    <span class="bl_sort-link"><a href="#">по цене</a></span>
                    <span class="bl_sort-link"><a href="#">по наименованию</a></span>
                </div>

                <ul class="search_result-bl clr">
                    <?php foreach ($items as $key=>$item) : ?>
                    <li>
                        <div class="flLeft">
                            <div class="search_result-id"><?php echo $key+1?>.</div>
                            <div class="search_result-photo"><a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$item['link'], 'manufacturer'=>$item['mlink'], 'language'=>  Language::getCurrentZone()))?>"><img src="<?php
                                                                            if (isset($item['image']['file']))
                                                                                echo Yii::app()->createUrl("site/download", array(
                                                                                    'id'=>@$item['image']['file'],
                                                                                    'filename'=>@$item['image']['link'],
                                                                                    'link'=>@$item['link'],
                                                                                    'language'=>  Language::getCurrentZone(),
                                                                                    'size' => ImagesModel::SIZE_MEDIUM
                                                                                )); 
                                                                            else
                                                                                echo "/assets/img/photo/informers/1.png";
                                        ?>"></a></div>
                            <div class="search_result-desc">
                                <h2 class="search_result-nameItem"><a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$item['link'], 'manufacturer'=>$item['mlink'], 'language'=>  Language::getCurrentZone()))?>">
                                    <?php echo $item['manufacturer']." ".$item['name']?>
                                </a></h2>
                                <p class="search_result-text">Описание....</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="flRight">
                            <div class="search_result-p_r">
                                <div class="search_result-price">15 000 р.</div>
                                <div class="search_result-rating">
                                    <ul class="rating">
                                         <li class="full"><a href="#">1</a></li>
                                         <li class=""><a href="#">2</a></li>
                                         <li class=""><a href="#">3</a></li>
                                         <li class=""><a href="#">4</a></li>
                                         <li class=""><a href="#">5</a></li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <!--
                    <li>
                        <div class="flLeft">
                            <div class="search_result-id">2.</div>
                            <div class="search_result-photo"><a href="#"><img src="img/photo/sam1.png"></a></div>
                            <div class="search_result-desc">
                                <h2 class="search_result-nameItem"><a href="#">Samsung Galaxy Note 8.0 N5100 16Gb</a></h2>
                                <p class="search_result-text">Операционная система iOS экран 9.7", 2048x1536, емкостный, мультитач встроенная память 128 Гб еспроводная связь Wi-Fi, Bluetooth, 3G, LTE навигация GPS вес 478 г тыловая камера 5 млн пикс.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="flRight">
                            <div class="search_result-p_r">
                                <div class="search_result-price">15 000 р.</div>
                                <div class="search_result-rating">
                                    <ul class="rating">
                                         <li class="full"><a href="#">1</a></li>
                                         <li class=""><a href="#">2</a></li>
                                         <li class=""><a href="#">3</a></li>
                                         <li class=""><a href="#">4</a></li>
                                         <li class=""><a href="#">5</a></li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="flLeft">
                            <div class="search_result-id">3.</div>
                            <div class="search_result-photo"><a href="#"><img src="img/photo/ipad.png"></a></div>
                            <div class="search_result-desc">
                                <h2 class="search_result-nameItem"><a href="#">Apple iPad mini 16Gb Wi-Fi</a></h2>
                                <p class="search_result-text">Операционная система iOS экран 9.7", 2048x1536, емкостный, мультитач встроенная память 128 Гб еспроводная связь Wi-Fi, Bluetooth, 3G, LTE навигация GPS вес 478 г тыловая камера 5 млн пикс.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="flRight">
                            <div class="search_result-p_r">
                                <div class="search_result-price">15 000 р.</div>
                                <div class="search_result-rating">
                                    <ul class="rating">
                                         <li class="full"><a href="#">1</a></li>
                                         <li class=""><a href="#">2</a></li>
                                         <li class=""><a href="#">3</a></li>
                                         <li class=""><a href="#">4</a></li>
                                         <li class=""><a href="#">5</a></li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="flLeft">
                            <div class="search_result-id">4.</div>
                            <div class="search_result-photo"><a href="#"><img src="img/photo/sam2.png"></a></div>
                            <div class="search_result-desc">
                                <h2 class="search_result-nameItem"><a href="#">Samsung Galaxy Tab 2 10.1 P5100 16Gb</a></h2>
                                <p class="search_result-text">Операционная система iOS экран 9.7", 2048x1536, емкостный, мультитач встроенная память 128 Гб еспроводная связь Wi-Fi, Bluetooth, 3G, LTE навигация GPS вес 478 г тыловая камера 5 млн пикс.</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="flRight">
                            <div class="search_result-p_r">
                                <div class="search_result-price">15 000 р.</div>
                                <div class="search_result-rating">
                                    <ul class="rating">
                                         <li class="full"><a href="#">1</a></li>
                                         <li class=""><a href="#">2</a></li>
                                         <li class=""><a href="#">3</a></li>
                                         <li class=""><a href="#">4</a></li>
                                         <li class=""><a href="#">5</a></li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    -->
                </ul>

                <nav id="pagination">
                    <a class="pag_prev" href="#">Предыдущая</a>
                    <a href="#">1</a>
                    <span>...</span>
                    <a href="#">5</a>
                    <a href="#">6</a>
                    <a href="#">7</a>
                    <a href="#">8</a>
                    <a href="#">9</a>
                    <a href="#">10</a>
                    <span>...</span>
                    <a href="#">22</a>
                    <a class="pag_next" href="#">Следующая</a>
                </nav>