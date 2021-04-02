@section('board-menu-message')
<!-- 伝言板メニュー[伝言作成]テンプレート -->
<div class="template message">
    <div class="content create_message">
        <div class="message-title">
            <input type="text" name="title" class="input-text" placeholder=" タイトルを入力してください" />
        </div>
        <div class="message-detail">
            <div class="detail-wrapper"><textarea name="message" class="text-area-detail" placeholder=" 伝言内容を入力してください"></textarea></div>
        </div>
        <div class="button-box"><button class="button bt1 reaction create">作成</button></div>
    </div>
</div>
@endsection