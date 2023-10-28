<template>
	<div>
		<NcModal v-if="open"
			:name="nomenclatureStore.addNewItem"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ nomenclatureStore.addNewItem }}</h2>

				<NcTextField label="Title" :value.sync="title" @keydown.enter.prevent="submit" />

				<NcButton :disabled="!title"
					type="primary"
					style="margin-top: 15px;"
					@click="submit">
					Submit
					<template #icon>
						<Plus v-if="!loading" :size="20" />
						<NcLoadingIcon v-else />
					</template>
				</NcButton>
			</div>
		</NcModal>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';

import Plus from "vue-material-design-icons/Plus.vue";

import { useItemsStore } from "../store/items.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcLoadingIcon,
		Plus,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			loading: false,
			title: "",
		};
	},
	computed: {
		...mapStores(useItemsStore, useNomenclatureStore),
	},
	methods: {
		closeModal() {
			this.$emit("update:open", false);
		},
		async submit() {
			if (this.title) {
				this.loading = true;
				try {
					await this.itemsStore.createItem({
						title: this.title,
					});
				} catch {

				} finally {
					this.loading = false;
					this.closeModal();
					this.title = "";
				}
			}
		},
	},
};
</script>
<style scoped>
.modal__content {
	margin: 50px;
}

.modal__content h2 {
	text-align: center;
}
</style>
