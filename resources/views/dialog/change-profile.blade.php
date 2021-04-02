@section('change-profile')
<!-- プロフィール編集のテンプレート -->
<div class="template change_profile">
    <div class="content change_profile">
        <input type="hidden" name="icon_name" value="" />
        <input type="hidden" name="icon" value="" />
        <ul>
            <li>
                <div class="label">アカウント名</div>
                <div class="dialo-input-text"><input type="text" class="account_name" placeholder="アカウント名を入力してください" value="{{$account_name}}" /></div>
            </li>
            <li>
                <input type="file" style="display:none;" />
                <div class="icon">
                    <span class="icon-select-button"></span>
                    <img src="{{$account_icon}}" />
                    <canvas></canvas>
                </div>
            </li>
        </ul>
        <div class="error-message"></div>
        <div class="button-box"><button class="button bt1 reaction save">保存</button></div>
    </div>
</div>
@endsection