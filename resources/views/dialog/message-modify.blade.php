@section('message-modify')
<!-- 伝言編集テンプレート -->
<div class="template modify_message">
    <div class="content modify_message">
        <div class="message-title">
            <input type="text" name="title" class="input-text" placeholder=" タイトルを入力してください" />
        </div>
        <div class="message-detail">
            <div class="detail-wrapper"><textarea name="message" class="text-area-detail" placeholder=" 伝言内容を入力してください"></textarea></div>
        </div>
        <div class="button-box"><button class="button bt1 reaction save">保存</button></div>
    </div>
</div>
@endsection