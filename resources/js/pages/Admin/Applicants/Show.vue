<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    applicant: any;
}>();

const formatDate = (dateString: string) => { // Helper for formatting date
     if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};

const Section = ({ title, children }: any) => { return null; }; // Dummy for layout understanding
</script>

<template>
    <Head :title="`Detail: ${applicant.full_name}`" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detail Pelamar: {{ applicant.full_name }}
                </h2>
                <Link href="/admin/applicants">
                    <Button variant="outline">Kembali</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Data Pribadi -->
                <Card>
                    <CardHeader><CardTitle>Data Pribadi</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><Label>NIK</Label><p>{{ applicant.nik }}</p></div>
                        <div><Label>Nama Lengkap</Label><p>{{ applicant.full_name }}</p></div>
                        <div><Label>Nama Panggilan</Label><p>{{ applicant.nickname || '-' }}</p></div>
                        <div><Label>Jenis Kelamin</Label><p>{{ applicant.gender }}</p></div>
                        <div><Label>Tempat, Tanggal Lahir</Label><p>{{ applicant.place_of_birth }}, {{ formatDate(applicant.date_of_birth) }}</p></div>
                        <div><Label>Agama</Label><p>{{ applicant.religion }}</p></div>
                        <div><Label>Status Pernikahan</Label><p>{{ applicant.marital_status }}</p></div>
                         <div><Label>Pendidikan Terakhir</Label><p>{{ applicant.last_education }}</p></div>
                        <div><Label>Alamat KTP</Label><p>{{ applicant.id_card_address }}</p></div>
                        <div><Label>Alamat Domisili</Label><p>{{ applicant.domicile_address }}</p></div>
                         <div><Label>Status Rumah</Label><p>{{ applicant.residence_ownership_status }}</p></div>
                        <div><Label>No HP</Label><p>{{ applicant.phone_number }}</p></div>
                        <div><Label>Email</Label><p>{{ applicant.email }}</p></div>
                    </CardContent>
                </Card>

                <!-- Data Keluarga -->
                <Card>
                    <CardHeader><CardTitle>Data Keluarga</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><Label>Nama Ayah</Label><p>{{ applicant.father_name }}</p></div>
                        <div><Label>Pekerjaan Ayah</Label><p>{{ applicant.father_occupation || '-' }}</p></div>
                        <div><Label>Nama Ibu</Label><p>{{ applicant.mother_name }}</p></div>
                        <div><Label>Pekerjaan Ibu</Label><p>{{ applicant.mother_occupation || '-' }}</p></div>
                        <div class="md:col-span-2"><Label>Alamat Orang Tua</Label><p>{{ applicant.parents_address || '-' }}</p></div>
                        
                        <div class="md:col-span-2 border-t pt-4 mt-2 font-medium">Suami/Istri</div>
                         <div><Label>Nama</Label><p>{{ applicant.spouse_name || '-' }}</p></div>
                         <div><Label>Usia</Label><p>{{ applicant.spouse_age ? applicant.spouse_age + ' Tahun' : '-' }}</p></div>
                         <div><Label>Pekerjaan</Label><p>{{ applicant.spouse_occupation || '-' }}</p></div>
                         <div><Label>No HP</Label><p>{{ applicant.spouse_phone || '-' }}</p></div>
                         <div class="md:col-span-2"><Label>Alamat</Label><p>{{ applicant.spouse_address || '-' }}</p></div>
                    </CardContent>
                </Card>

                <!-- Pengalaman Kerja -->
                <Card>
                     <CardHeader><CardTitle>Pengalaman Kerja</CardTitle></CardHeader>
                     <CardContent>
                        <div v-if="applicant.work_experiences.length === 0" class="text-gray-500">Tidak ada data pengalaman kerja.</div>
                        <div v-else class="space-y-4">
                            <div v-for="work in applicant.work_experiences" :key="work.id" class="border p-3 rounded">
                                <p class="font-bold">{{ work.company_name }} <span class="text-gray-500 font-normal">({{ work.start_year }} - {{ work.end_year }})</span></p>
                                <p class="text-sm font-medium">{{ work.position }}</p>
                                <p class="text-sm text-gray-600">{{ work.company_address }}</p>
                            </div>
                        </div>
                     </CardContent>
                </Card>
                 
                <!-- Data Anak -->
                <Card>
                     <CardHeader><CardTitle>Data Anak</CardTitle></CardHeader>
                     <CardContent>
                        <div v-if="applicant.children.length === 0" class="text-gray-500">Tidak ada data anak.</div>
                        <div v-else class="space-y-4">
                            <div v-for="child in applicant.children" :key="child.id" class="border p-3 rounded grid grid-cols-2 lg:grid-cols-4 gap-2">
                                <div><Label>Nama</Label><p>{{ child.name }}</p></div>
                                <div><Label>Umur</Label><p>{{ child.age }} Tahun</p></div>
                                <div><Label>Gender</Label><p>{{ child.gender }}</p></div>
                                <div><Label>Pendidikan</Label><p>{{ child.education || '-' }}</p></div>
                            </div>
                        </div>
                     </CardContent>
                </Card>

                <!-- Kontak Darurat -->
                <Card>
                     <CardHeader><CardTitle>Kontak Darurat</CardTitle></CardHeader>
                     <CardContent>
                        <div v-if="applicant.emergency_contacts.length === 0" class="text-gray-500">Tidak ada data kontak darurat.</div>
                        <div v-else class="space-y-4">
                            <div v-for="contact in applicant.emergency_contacts" :key="contact.id" class="border p-3 rounded grid grid-cols-3 gap-2">
                                <div><Label>Nama</Label><p>{{ contact.name }}</p></div>
                                <div><Label>Hubungan</Label><p>{{ contact.relationship }}</p></div>
                                <div><Label>No HP</Label><p>{{ contact.phone_number }}</p></div>
                            </div>
                        </div>
                     </CardContent>
                </Card>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
p {
    @apply font-medium text-gray-900;
}
label {
    @apply text-xs text-gray-500 block mb-1;
}
</style>
