<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps<{
    applicants: {
        data: Array<{
            id: number;
            full_name: string;
            email: string;
            phone_number: string;
            created_at: string;
        }>;
        links: Array<any>;
    };
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Data Pelamar" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Data Pelamar
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Daftar Pelamar</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Lengkap
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No HP
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Daftar
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="applicants.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada data pelamar.
                                        </td>
                                    </tr>
                                    <tr v-for="applicant in applicants.data" :key="applicant.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ applicant.full_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ applicant.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ applicant.phone_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ formatDate(applicant.created_at) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="`/admin/applicants/${applicant.id}`" class="text-indigo-600 hover:text-indigo-900">
                                                Lihat Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Logic (Simplified) -->
                        <div class="mt-4 flex justify-between items-center" v-if="applicants.data.length > 0">
                            <!-- Use Inertia Links for pagination here if needed, keeping it simple for now -->
                             <div class="text-sm text-gray-500">
                                Menampilkan {{ applicants.data.length }} data.
                            </div>
                        </div>

                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
