@section('search')
<div class="content search">
    <div class="content-inner">
        <div class="search-keyword">
            <input type="text" class="input-search-keyword" placeholder="キーワード検索" /><span class="search-icon"></span>
        </div>
        <div class="search-results">
            <div class="search-result-list">
                <ul></ul>
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