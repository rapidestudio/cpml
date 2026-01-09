<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

import { useForm } from '@inertiajs/vue3';
import { Printer, MessageCircle, Save, FileText } from 'lucide-vue-next';

const props = defineProps<{
    applicant: any;
    questions: Array<{ id: number, text: string }>;
}>();

const form = useForm({
    status: props.applicant.status || 'new',
    notes: props.applicant.notes || '',
});
// ... (rest of script)

const updateStatus = () => {
    form.put(`/admin/applicants/${props.applicant.id}`, {
        preserveScroll: true,
        only: ['applicant', 'flash'],
    });
};

const print = () => {
    window.print();
};

const getWhatsAppLink = (phone: string, name: string) => {
    let number = phone.replace(/\D/g, '');
    if (number.startsWith('0')) {
        number = '62' + number.slice(1);
    }
    const text = `Halo ${name}, kami telah mereview lamaran Anda...`;
    return `https://wa.me/${number}?text=${encodeURIComponent(text)}`;
};

const statusOptions = [
    { value: 'new', label: 'Baru' },
    { value: 'screening', label: 'Seleksi Berkas' },
    { value: 'interview', label: 'Wawancara' },
    { value: 'accepted', label: 'Diterima' },
    { value: 'rejected', label: 'Ditolak' },
];

const formatDate = (dateString: string) => {
     if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};

const getChecklistAnswer = (questionId: number) => {
    if (!props.applicant.checklist) return null;
    return props.applicant.checklist[questionId];
};
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

        <div class="py-12 print:py-0">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6 print:space-y-0 print:grid print:grid-cols-12 print:gap-4 print:w-full print:max-w-none print:px-0 print:text-xs print:leading-tight">
                
                <!-- Print Header (Removed) -->
                <!-- Admin Tools (Hidden on Print) -->
                <Card class="print:hidden border-blue-200 bg-blue-50 dark:bg-blue-900/20">
                    <CardHeader>
                        <CardTitle class="text-blue-800 dark:text-blue-300">Admin Tools</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label>Status Lamaran</Label>
                                <div class="flex gap-2">
                                    <select v-model="form.status" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 dark:bg-gray-950 dark:text-white">
                                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                    <Button @click="updateStatus" :disabled="form.processing">
                                        <Save class="w-4 h-4 mr-2" /> Simpan
                                    </Button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label>Aksi Cepat</Label>
                                <div class="flex gap-2">
                                    <Button variant="outline" @click="print">
                                        <Printer class="w-4 h-4 mr-2" /> Cetak PDF
                                    </Button>
                                    <a :href="getWhatsAppLink(applicant.phone_number, applicant.full_name)" target="_blank">
                                        <Button variant="outline" class="text-green-600 border-green-200 hover:bg-green-50">
                                            <MessageCircle class="w-4 h-4 mr-2" /> WhatsApp
                                        </Button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label>Catatan Internal (Hanya dilihat Admin)</Label>
                            <div class="flex gap-2">
                                <textarea v-model="form.notes" placeholder="Tulis catatan interview, negosiasi gaji, dll..." class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-950 dark:text-white dark:border-gray-800"></textarea>
                                <Button @click="updateStatus" :disabled="form.processing" size="icon" title="Simpan Catatan">
                                    <Save class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Data Pribadi -->
                <Card class="print:shadow-none print:border-none print:p-0 print:col-span-9">
                    <CardHeader class="print:py-1 print:px-0"><CardTitle class="print:text-sm">Data Pribadi</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4 print:grid-cols-4 print:gap-2 print:p-0">
                        <div class="md:col-span-2"><Label>Posisi yang Dilamar</Label><p class="text-lg font-bold text-blue-600">{{ applicant.position?.name || '-' }}</p></div>
                        <div><Label>NIK</Label><p>{{ applicant.nik }}</p></div>
                        <div><Label>Nama Lengkap</Label><p>{{ applicant.full_name }}</p></div>
                        <div><Label>Nama Panggilan</Label><p>{{ applicant.nickname || '-' }}</p></div>
                        <div><Label>Jenis Kelamin</Label><p>{{ applicant.gender }}</p></div>
                        <div><Label>Tempat, Tanggal Lahir</Label><p>{{ applicant.place_of_birth }}, {{ formatDate(applicant.date_of_birth) }}</p></div>
                        <div><Label>Tinggi / Berat Badan</Label><p>{{ applicant.height ? applicant.height + ' cm' : '-' }} / {{ applicant.weight ? applicant.weight + ' kg' : '-' }}</p></div>
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

                <!-- Dokumen Pendukung (Foto Only on Print) -->
                <Card class="print:shadow-none print:border-none print:break-inside-avoid print:bg-transparent print:col-span-3">
                    <CardHeader class="print:hidden"><CardTitle>Dokumen Pendukung</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-6 print:block print:p-0">
                        <div class="border rounded-lg p-4 flex flex-col items-center space-y-3 print:border-none print:p-0 print:items-center">
                            <span class="font-medium text-sm print:hidden">Pas Foto</span>
                            <div v-if="applicant.photo_path" class="w-32 h-40 bg-gray-100 rounded overflow-hidden print:w-32 print:h-40 print:rounded-none">
                                <img :src="`/storage/${applicant.photo_path}`" alt="Foto Pelamar" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="text-gray-400 text-sm italic">Tidak ada foto</div>
                            <a v-if="applicant.photo_path" :href="`/storage/${applicant.photo_path}`" target="_blank" class="text-blue-600 text-xs hover:underline print:hidden">Lihat Full</a>
                        </div>
                        <div class="border rounded-lg p-4 flex flex-col items-center space-y-3 print:hidden">
                            <span class="font-medium text-sm">Scan KTP</span>
                            <a v-if="applicant.ktp_path" :href="`/storage/${applicant.ktp_path}`" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800">
                                <FileText class="w-8 h-8 mr-2" />
                                <span class="text-sm">Lihat KTP (PDF)</span>
                            </a>
                            <div v-else class="text-gray-400 text-sm italic">Tidak ada KTP</div>
                        </div>
                        <div class="border rounded-lg p-4 flex flex-col items-center space-y-3 print:hidden">
                             <span class="font-medium text-sm">Curriculum Vitae</span>
                            <a v-if="applicant.cv_path" :href="`/storage/${applicant.cv_path}`" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800">
                                <FileText class="w-8 h-8 mr-2" />
                                <span class="text-sm">Lihat CV (PDF)</span>
                            </a>
                            <div v-else class="text-gray-400 text-sm italic">Tidak ada CV</div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Data Keluarga -->
                <Card class="print:shadow-none print:border-none print:break-inside-avoid print:p-0 print:col-span-12">
                    <CardHeader class="print:py-1 print:px-0"><CardTitle class="print:text-sm">Data Keluarga</CardTitle></CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4 print:grid-cols-4 print:gap-2 print:p-0">
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
                <Card class="print:shadow-none print:border-none print:break-inside-avoid print:col-span-12">
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
                <Card class="print:shadow-none print:border-none print:break-inside-avoid print:col-span-12 print:hidden">
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
                <Card class="print:shadow-none print:border-none print:break-inside-avoid print:col-span-12">
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

                <!-- Checklist / Daftar Pertanyaan -->
                <Card class="print:shadow-none print:border-none print:break-before-page print:p-0 print:col-span-12">
                     <CardHeader class="print:py-1 print:px-0"><CardTitle class="print:text-sm">Daftar Pertanyaan</CardTitle></CardHeader>
                     <CardContent class="print:p-0">
                        <div v-if="!applicant.checklist || Object.keys(applicant.checklist).length === 0" class="text-gray-500">Tidak ada data checklist.</div>
                        <div v-else class="space-y-4 print:space-y-2 print:grid print:grid-cols-2 print:gap-x-4 print:gap-y-2">
                            <div v-for="question in questions" :key="question.id" class="border p-4 rounded bg-gray-50 dark:bg-gray-800 print:border p-2 print:bg-transparent print:text-xs">
                                <p class="mb-2 font-medium text-sm text-gray-900">{{ question.text }}</p>
                                <div v-if="getChecklistAnswer(question.id)">
                                     <div class="flex items-center space-x-2 text-sm">
                                        <span class="font-bold" :class="getChecklistAnswer(question.id).answer === 'Ya' ? 'text-green-600' : 'text-red-600'">
                                            {{ getChecklistAnswer(question.id).answer }}
                                        </span>
                                        <span v-if="getChecklistAnswer(question.id).note" class="text-gray-500">- {{ getChecklistAnswer(question.id).note }}</span>
                                     </div>
                                </div>
                                <div v-else class="text-sm text-gray-400 italic">Belum dijawab</div>
                            </div>
                        </div>
                     </CardContent>
                </Card>

                <div class="flex justify-end mt-6 print:hidden">
                    <Link href="/admin/applicants">
                        <Button variant="outline">Kembali ke Daftar</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: auto;
        margin: 5mm;
    }
    body {
        -webkit-print-color-adjust: exact;
    }
}
</style>


