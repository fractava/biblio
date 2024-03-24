<template>
	<div>
		<NcModal v-if="open"
			:name="nomenclatureStore.addNewCustomer"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ nomenclatureStore.addNewCustomer }}</h2>

				<NcTextField label="Name" :value.sync="name" @keydown.enter.prevent="submit" />

				<NcButton :disabled="!name"
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
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon.js";

import Plus from "vue-material-design-icons/Plus.vue";

import { useCustomersStore } from "../store/customers.js";
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
			name: "",
		};
	},
	computed: {
		...mapStores(useCustomersStore, useNomenclatureStore),
	},
	methods: {
		showModal() {
			this.$emit("update:open", true);
		},
		closeModal() {
			this.$emit("update:open", false);
		},
		async submit() {
			if (this.name) {
				this.loading = true;
				try {
					await this.customersStore.createCustomer({
						name: this.name,
					});
				} catch {

				} finally {
					this.loading = false;
					this.closeModal();
					this.name = "";
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
