import { createRouter, createWebHistory } from 'vue-router';
import AddressBookList from './components/AddressBookList.vue';
import AddressBookForm from './components/AddressBookForm.vue';
import GroupList from './components/GroupList.vue';
import GroupForm from './components/GroupForm.vue';

const routes = [
    {
        path: '/',
        name: 'address-book-list',
        component: AddressBookList,
    },
    {
        path: '/addresses/create',
        name: 'address-book-create',
        component: AddressBookForm,
    },
    {
        path: '/addresses/:id/edit',
        name: 'address-book-edit',
        component: AddressBookForm,
        props: true,
    },
    {
        path: '/groups',
        name: 'group-list',
        component: GroupList,
    },
    {
        path: '/groups/create',
        name: 'group-create',
        component: GroupForm,
    },
    {
        path: '/groups/:id/edit',
        name: 'group-edit',
        component: GroupForm,
        props: true,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;