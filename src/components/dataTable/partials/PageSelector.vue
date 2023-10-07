<template>
	<div class="tablePageSelector">
		<NcButton class="tablePageSelectorButton"
			:close-after-click="true"
			:aria-label="t('biblio', 'Decrement page')"
			type="secondary"
			@click="down">
			<template #icon>
				<ChevronLeft :size="25" />
			</template>
		</NcButton>
		<NcButton class="tablePageSelectorButton"
			:close-after-click="true"
			:aria-label="t('biblio', 'Increment page')"
			type="secondary"
			@click="down">
			<template #icon>
				<ChevronRight :size="25" />
			</template>
		</NcButton>
	</div>
</template>
<script>
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import ChevronLeft from "vue-material-design-icons/ChevronLeft.vue";
import ChevronRight from "vue-material-design-icons/ChevronRight.vue";

export default {
	components: {
		NcButton,
		ChevronLeft,
		ChevronRight,
	},
	methods: {
		up() {

		},
		down() {
			this.$emit('update:page')
		},
	},
};
</script>
<style lang="scss" scoped>
.tablePageSelector {
	display: flex;
	flex-flow: row nowrap;

	.tablePageSelectorButton {
		&:first-child {
			border-top-right-radius: 0px;
			border-bottom-right-radius: 0px;
		}
		&:last-child {
			border-top-left-radius: 0px;
			border-bottom-left-radius: 0px;
		}
	}
}
</style>
