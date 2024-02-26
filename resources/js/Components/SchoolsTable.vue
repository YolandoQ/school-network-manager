<script>
import { ref, watch } from 'vue';
import Dropdown from './Dropdown.vue';
import Pagination from './Pagination.vue';
import Modal from './Modal.vue';

export default {
    props: {
        data: Object,
        schools: {
            type: Object,
            default: {}
        }
    },
    components: {
        Pagination,
        Dropdown,
        Modal,
    },
    setup(props) {
        const isUpdating = ref(false);
        const showModalEditSchools = ref(false);
        const showModalcreateSchools = ref(false);
        const maxWidth = ref('md');
        const closeable = ref(true);
        // const showAddStudentForm = ref(false);
        const editedSchool = ref({
            id: null,
            name: '',
        });
        const newSchool = ref({
            name: '',
            email: '',
        });

        const openEditModalSchools = (school) => {
            showModalEditSchools.value = true;
            editedSchool.value.id = school.id;
            editedSchool.value.name = school.name;
        };

        const editModalSchools = () => {
            showModalEditSchools.value = false;
        };

        const handleEditSubmitSchools = async () => {
            isUpdating.value = true;
            try {
                const response = await axios.put(`/api/schools/${editedSchool.value.id}`, {
                    name: editedSchool.value.name,
                });

                editModalSchools();
                alert(response.data.message)
                window.location.reload();
            } catch (error) {
                editModalSchools();
                alert('Falha durante atualização');
                window.location.reload();
            } finally {
                isUpdating.value = false;
            }
        };

        const deleteSchool = async (schoolId) => {

            isUpdating.value = true;
            if (!confirm("Confirma a exclusão dessa escola(todas as turmas e alunos serão deletados)?")) {
                return;
            }
            try {
                const response = await axios.delete(`/api/schools/${schoolId}`);
                alert(response.data.message)
                window.location.reload();
            } catch (error) {
                alert('Falha durante exclusão');
            } finally {
                isUpdating.value = false;
            }
        };

        // const openCreateModalSchools = () => {
        //     showModalcreateSchools.value = true;
        // };

        const closeCreateModalSchools = () => {
            showModalcreateSchools.value = false;
        };

        const openAddSchoolForm = () => {
            showModalcreateSchools.value = true;
        };

        const handleAddSubmitSchool = async () => {
            isUpdating.value = true;
            try {
                const response = await axios.post('/api/schools/create', {
                    name: newSchool.value.name,
                });

                closeCreateModalSchools();
                alert(response.data.message);
                window.location.reload();
            } catch (error) {
                closeCreateModalSchools();
                alert('Falha durante a adição da escola');
                window.location.reload();
            } finally {
                isUpdating.value = false;
            }
        };
        return {
            showModalcreateSchools,
            showModalEditSchools,
            maxWidth,
            closeable,
            isUpdating,
            editedSchool,
            openEditModalSchools,
            editModalSchools,
            handleEditSubmitSchools,
            deleteSchool,
            newSchool,
            closeCreateModalSchools,
            handleAddSubmitSchool,
            openAddSchoolForm,
        };
    },
};
</script>

<template>
    <div class="not-prose relative bg-slate-50 rounded-xl overflow-hidden ">
        <div class="flex justify-end py-2 px-4 mt-4">
            <button v-if="$page.props.auth.user" @click="() => openAddSchoolForm()"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5  text-center">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14m-7 7V5" />
                </svg>
                Adicionar escola
            </button>

        </div>
        <div v-if="data.data.length > 0" class="relative rounded-xl overflow-auto ">
            <div class="shadow-sm overflow-hidden my-4">
                <div class="relative overflow-x-auto">

                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 my-8">

                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Nome(escola)
                                </th>

                                <th v-if="$page.props.auth.user" scope="col" class="px-6 py-3">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="school in data.data" :key="school.id">
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">
                                        {{ school.name }}
                                    </td>
                                    <td v-if="$page.props.auth.user" class="px-6 py-4 flex items-center ">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <button @click="() => openEditModalSchools(school)" type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                                <svg class="w-3 h-3 me-2" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z" />
                                                </svg>
                                                Editar
                                            </button>
                                            <button @click="() => deleteSchool(school.id)" type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700">
                                                <svg class="w-3 h-3 me-2" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M20 10H4v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8ZM9 13v-1h6v1c0 .6-.4 1-1 1h-4a1 1 0 0 1-1-1Z"
                                                        clip-rule="evenodd" />
                                                    <path d="M2 6c0-1.1.9-2 2-2h16a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Z" />
                                                </svg>

                                                Deletar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <Pagination :links="data" />
            <div id="editModalSchools">
                <Modal :show="showModalEditSchools" :maxWidth="maxWidth" :closeable="closeable" @close="editModalSchools">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow">
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                                <div class="">
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 ">
                                    Atualize o registro da escola
                                </h3>
                                <button @click="editModalSchools" type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                    data-modal-toggle="editModalSchools">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <form @submit.prevent="handleEditSubmitSchools" class="p-4 md:p-5"
                                :class="{ 'jet-loading': isUpdating }">
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-span-2">
                                        <label for="editNameSchool"
                                            class="block mb-2 text-sm font-medium text-gray-900 ">Nome</label>
                                        <input v-model="editedSchool.name" id="editNameSchool" type="text" name="name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                            required="">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <span v-if="isUpdating" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"> <svg aria-hidden="true"
                                            class="inline w-8 h-8 text-gray-200 animate-spin fill-gray-600 "
                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor" />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill" />
                                        </svg></span>
                                    <span v-else>Salvar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </Modal>
            </div>

        </div>
        <div v-else class="text-center px-4 py-4">
            <p> Nenhum registro encontrado.</p>
        </div>
        <div id="createModalSchools">
            <Modal :show="showModalcreateSchools" :maxWidth="maxWidth" :closeable="closeable"
                @close="closeCreateModalSchools">
                <div class="relative w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                            <h3 class="text-lg font-semibold text-gray-900 ">
                                Adicione um novo registro de escola
                            </h3>
                            <button @click="closeCreateModalSchools" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                data-modal-toggle="createModalSchools">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>
                        <form @submit.prevent="handleAddSubmitSchool" class="p-4 md:p-5"
                            :class="{ 'jet-loading': isUpdating }">
                            <div class="grid gap-4 mb-4 grid-cols-2">
                                <div class="col-span-2">
                                    <label for="createNameSchool"
                                        class="block mb-2 text-sm font-medium text-gray-900 ">Nome</label>
                                    <input v-model="newSchool.name" id="createNameSchool" type="text" name="name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required="" placeholder="Nome da escola" />
                                </div>
                            </div>
                            <button type="submit"
                                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <span v-if="isUpdating" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"> <svg aria-hidden="true"
                                        class="inline w-8 h-8 text-gray-200 animate-spin fill-gray-600 "
                                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="currentColor" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentFill" />
                                    </svg></span>
                                <span v-else>Salvar</span>
                            </button>
                        </form>
                </div>
            </div>
        </Modal>
    </div>
    <div class="absolute inset-0 pointer-events-none border border-black/5 rounded-xl dark:border-white/5">
    </div>
</div></template>

