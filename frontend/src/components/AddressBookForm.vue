<template>
    <div class="address-book-form">
        <h2>{{ isEdit ? 'Edit Address' : 'Create Address' }}</h2>
        <b-form @submit.prevent="submitForm">
            <b-form-group label="Last Name" label-for="last-name">
                <b-form-input id="last-name" v-model="address.last_name" required/>
            </b-form-group>

            <b-form-group label="First Name" label-for="first-name">
                <b-form-input id="first-name" v-model="address.first_name" required/>
            </b-form-group>

            <b-form-group label="Email" label-for="email">
                <b-form-input id="email" v-model="address.email" type="email" required/>
            </b-form-group>

            <b-form-group label="Street" label-for="street">
                <b-form-input id="street" v-model="address.street" required/>
            </b-form-group>

            <b-form-group label="Zip Code" label-for="zip-code">
                <b-form-input id="zip-code" v-model="address.zip_code" required/>
            </b-form-group>

            <b-form-group label="City" label-for="city">
                <b-form-select id="city" v-model="address.city_id" :options="cities" value-field="id" text-field="name" required/>
            </b-form-group>

            <b-form-group label="Tags" label-for="tags">
                <b-form-checkbox-group id="tags" v-model="address.tag_ids" :options="tags" value-field="id" text-field="name" />
            </b-form-group>

            <b-form-group label="New Tags" label-for="new-tags">
                <b-form-input id="new-tags" v-model="address.new_tags" placeholder="Enter new tags (comma-separated)" />
            </b-form-group>

            <b-button type="submit" variant="primary" class="mx-2">{{ isEdit ? 'Update' : 'Create' }}</b-button>
            <b-button type="button" variant="secondary" @click="navigateBack">Cancel</b-button>
        </b-form>
    </div>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BFormSelect, BButton, BFormCheckboxGroup } from 'bootstrap-vue-next';

export default {
    name: 'AddressBookForm',
    components: {
        BForm,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BFormCheckboxGroup,
        BButton,
    },
    props: {
        id: {
            type: [Number, String],
            default: null,
        },
    },
    data() {
        return {
            address: {
                last_name: '',
                first_name: '',
                email: '',
                street: '',
                zip_code: '',
                city_id: null,
                tag_ids: [],
                new_tags: '',
            },
            tags: [],
            cities: [],
            isEdit: false,
        };
    },
    methods: {
        async fetchAddress() {
            try {
                const response = await this.$http.get(`/addresses/${this.id}`);
                this.address = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchCities() {
            try {
                const response = await this.$http.get('/cities');
                this.cities = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async submitForm() {
            try {
                if (this.isEdit) {
                    await this.$http.put(`/addresses/${this.id}`, this.address);
                } else {
                    await this.$http.post('/addresses', this.address);
                }
                this.navigateBack();
            } catch (error) {
                console.error(error);
            }
        },
        navigateBack() {
            this.$router.push({ name: 'address-book-list' });
        },
        async fetchTags() {
            try {
                const response = await this.$http.get('/tags');
                this.tags = response.data;
            } catch (error) {
                console.error(error);
            }
        },
    },
    async mounted() {
        await this.fetchCities();
        await this.fetchTags();
        if (this.id) {
            this.isEdit = true;
            await this.fetchAddress();
        }
    },
};
</script>
