<template>
	<div class="container">
		<slot />
	</div>
</template>
<style>
.container {
	display: flex;
	flex-direction: row;
	justify-content: center;

	.button-vue:not(:first-child) {
		border-top-left-radius: 0px;
		border-bottom-left-radius: 0px;
	}

	.button-vue:not(:last-child) {
		border-top-right-radius: 0px;
		border-bottom-right-radius: 0px;
	}
}
</style>
