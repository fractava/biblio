<template>
	<div class="tablePageSelector">
		<NcButton class="tablePageSelectorButton"
			:close-after-click="true"
			:aria-label="t('biblio', 'Decrement page')"
			:disabled="page <= 1"
			type="secondary"
			@click="down">
			<template #icon>
				<ChevronLeft :size="25" />
			</template>
		</NcButton>
		<vueSelect class="pageSelect"
			:options="pages"
			:value="page"
			:placeholder="t('biblio', 'Select Page')"
			:clearable="false"
			@input="setPage" />
		<NcButton class="tablePageSelectorButton"
			:close-after-click="true"
			:aria-label="t('biblio', 'Increment page')"
			:disabled="page >= maxPage"
			type="secondary"
			@click="up">
			<template #icon>
				<ChevronRight :size="25" />
			</template>
		</NcButton>
	</div>
</template>
<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js"
import vueSelect from "vue-select";

import ChevronLeft from "vue-material-design-icons/ChevronLeft.vue";
import ChevronRight from "vue-material-design-icons/ChevronRight.vue";

import generalHelper from "../../mixins/generalHelper";

export default {
	components: {
		NcButton,
		vueSelect,
		ChevronLeft,
		ChevronRight,
	},
	mixins: [generalHelper],
	props: {
		page: {
			type: Number,
			default: 1,
		},
		maxPage: {
			type: Number,
			default: 1,
		},
	},
	computed: {
		pages() {
			return this.range(1, this.maxPage + 1);
		},
	},
	methods: {
		up() {
			if (this.page < this.maxPage) {
				this.setPage(this.page + 1);
			}
		},
		down() {
			if (this.page > 1) {
				this.setPage(this.page - 1);
			}
		},
		setPage(pageNumber) {
			this.$emit("update:page", pageNumber);
		},
	},
};
</script>
<style lang="scss">
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

	.pageSelect {
		--vs-search-input-bg: var(--color-primary-element-light);
		--vs-dropdown-bg: var(--color-primary-element-light);
		--vs-dropdown-option--active-bg: var(--color-primary-element-light-hover);
		--vs-controls-color: var(--color-primary-element-light-text);
		--vs-search-input-color: var(--color-primary-element-light-text);
		--vs-border-color: transparent;
		--vs-dropdown-min-width: 96px;

		width: 96px;
		margin: 0px;

		.vs__dropdown-toggle {
			border-radius: 0px;

			&:hover {
				background-color: var(--color-primary-element-light-hover);
			}
		}

		.vs__dropdown-menu {
			border-color: var(--vs-border-color) !important;
		}
	}
}
</style>
