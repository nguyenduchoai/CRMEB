<template>
	<view class="feedback-wrapper">
		<view class="head">
			<view class="left-wrapper">
				<view class="title">{{$t(`CSKH của cửa hàng hiện không trực tuyến`)}}</view>
				<view class="txt">{{feedback}}</view>
			</view>
			<view class="img-box"><image src="../static/feed-icon.png" mode=""></image></view>
		</view>
		<view class="main">
			<view class="title">{{$t(`Gửi phản hồi`)}}</view>
			<view class="input-box">
				<input type="text" :placeholder="$t(`Vui lòng nhập họ tên`)" v-model="name">
			</view>
			<view class="input-box">
				<input type="text" :placeholder="$t(`Vui lòng nhập số điện thoại`)" v-model="phone">
			</view>
			<view class="input-box">
				<textarea type="text" :placeholder="$t(`Vui lòng điền nội dung`)" v-model="con" />
			</view>
			<view class="sub_btn" @click="subMit">{{$t(`Gửi`)}}</view>
		</view>
	</view>
</template>

<script>
	import { serviceFeedBack,feedBackPost } from '@/api/kefu.js'
	export default{
		name:'feedback',
		data(){
			return {
				name:'',
				phone:'',
				con:'',
				feedback:''
			}
		},
		onLoad(){
			this.getInfo()
		},
		methods:{
			getInfo(){
				serviceFeedBack().then(res=>{
					this.feedback = res.data.feedback
				})
			},
			subMit(){
				if(!this.name){
					return this.$util.Tips({
						title: this.$t(`Vui lòng nhập họ tên`)
					})
				}
				if(!this.phone || !(/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.phone))){
					return this.$util.Tips({
						title:this.$t(`Vui lòng nhập đúng số điện thoại`)
					})
				}
				if(!this.con){
					return this.$util.Tips({
						title: this.$t(`Vui lòng điền nội dung`)
					})
				}
				feedBackPost({
					rela_name:this.name,
					phone:this.phone,
					content:this.con
				}).then(res=>{
					this.$util.Tips({
						title:res.msg,
						icon:'success'
					},{
						tab:3
					})
				}).catch(function(res) {
					that.$util.Tips({ title: res });
				});
			}
		}
	}
</script>

<style lang="stylus">
	.feedback-wrapper
		.head
			display flex
			align-items center
			justify-content space-between
			height 215rpx
			padding 0rpx 30rpx
			background-color #3A3A3A
			.left-wrapper
				width  456rpx
				color #fff
				font-size 24rpx
				.title
					margin-bottom 15rpx
					font-size 32rpx
			.img-box
				image
					width 173rpx
					height 156rpx
		.info
			display flex
			background-color #fff
			.info-item
				flex 1
				display flex
				flex-direction column
				align-items center
				justify-content center
				height 138rpx
				border-right 1px solid #F0F1F2
				&:last-child
					border:none
				.big-txt
					font-size 32rpx
					font-weight bold
					color #282828
				.small
					margin-top 10rpx
					font-size 24rpx
					color #9F9F9F
		.main
			margin-top 16rpx
			padding 30rpx 30rpx 68rpx
			background-color #FFF
			.title
				font-size 30rpx
				font-weight bold
			.input-box
				margin-top 20rpx
				input
					display block
					width 100%
					height 78rpx
					background #F5F5F5
					font-size 28rpx
					padding-left 20rpx
				textarea
					display block
					width 100%
					height 260rpx
					padding 20rpx
					background #F5F5F5
					font-size 28rpx
			.sub_btn
				margin-top 130rpx
				width 100%
				height 86rpx
				line-height 86rpx
				font-size 30rpx
				text-align center
				color #fff
				border-radius 43rpx
				background-color #3875EA
</style>
