<template>
	<div>
		<NcModal v-if="open"
			:name="t('biblio', 'Add new Item')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new Item') }}</h2>

				<NcTextField label="Title" :value.sync="title" />

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

import Plus from "vue-material-design-icons/Plus";

import { useBiblioStore } from "../store/biblio.js";

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
		...mapStores(useBiblioStore),
	},
	methods: {
		showModal() {
			this.$emit("update:open", true);
		},
		closeModal() {
			this.$emit("update:open", false);
		},
		async submit() {
			this.loading = true;
			try {
				await this.biblioStore.createItem({
					title: this.title,
				});
			} catch {

			} finally {
				this.loading = false;
				this.closeModal();
				this.title = "";
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