@section('search')
<div class="content search">
    <div class="content-inner">
        <div class="search-keyword">
            <input type="text" class="input-search-keyword" placeholder="キーワード検索" /><span class="search-icon"></span>
        </div>
        <div class="search-results">
            <div class="search-result-page-wrapper">
                <div class="search-result-page head">
                    <span class="search-result-page-prev page-btn" data-page="prev">
                        <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <polygon points="419.916,71.821 348.084,0 92.084,256.005 348.084,512 419.916,440.178 235.742,256.005 	" style="fill: rgb(75, 75, 75);"></polygon>
                            </g>
                        </svg>
                    </span>
                    <ul></ul>
                    <span class="search-result-page-next page-btn" data-page="next">
                        <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <polygon points="163.916,0 92.084,71.822 276.258,255.996 92.084,440.178 163.916,512 419.916,255.996 " style="fill: rgb(75, 75, 75);"></polygon>
                            </g>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="search-result-list">
                <ul class="search-result-board"></ul>
            </div>
            <div class="search-result-page-wrapper">
                <div class="search-result-page foot">
                    <span class="search-result-page-prev page-btn" data-page="prev">
                        <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <polygon points="419.916,71.821 348.084,0 92.084,256.005 348.084,512 419.916,440.178 235.742,256.005 	" style="fill: rgb(75, 75, 75);"></polygon>
                            </g>
                        </svg>
                    </span>
                    <ul></ul>
                    <span class="search-result-page-next page-btn" data-page="next">
                        <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <polygon points="163.916,0 92.084,71.822 276.258,255.996 92.084,440.178 163.916,512 419.916,255.996 " style="fill: rgb(75, 75, 75);"></polygon>
                            </g>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="template">
        <div class="label">
            <div class="creater text-overflow-ellipsis"></div>
            <div class="title text-overflow-ellipsis"></div>
        </div>
        <div class="join-button"><button class="button bt1"></button></div>
        
        <!-- 参加パスワード入力用のダイアログテンプレート -->
        <div class="template join">
            <div class="content join_dengonban">
                <input type="hidden" name="dgb_id" value=""/>
                <div class="dialo-input-text"><input type="text" class="password" placeholder=" パスワード" /></div>
                <div class="error-message"></div>
                <div class="button-box"><button class="button bt1 reaction ok">OK</button></div>
            </div>
        </div>
    </div>
</div>
@endsection