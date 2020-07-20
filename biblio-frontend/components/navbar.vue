<template>
	<div>
		<md-tabs md-sync-route>
			<md-tab id="tab-home" md-label="Medien E/A" to="/home" exact></md-tab>
			<md-tab id="tab-catalog" md-label="Katalogisierung" to="/catalog"></md-tab>
			<md-tab id="tab-students" md-label="Schüler" to="/students"></md-tab>
			<md-tab id="tab-lists" md-label="Listen" to="/lists"></md-tab>
			<md-tab id="tab-admin" md-label="Verwaltung" to="/admin"></md-tab>
		</md-tabs>
	</div>
</template>

<script>
export default {
	name: "navbar"
};
</script>
