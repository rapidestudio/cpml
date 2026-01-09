<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
// import { Checkbox } from '@/components/ui/checkbox';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps<{
    questions: Array<{ id: number, text: string }>;
    positions: Array<{ id: number, name: string }>;
    position_id?: string | number;
}>();

const form = useForm({
    // Personal Data
    position_id: props.position_id || '',
    website: '', // Honeypot
    nik: '',
    full_name: '',
    nickname: '',
    gender: '',
    place_of_birth: '',
    date_of_birth: '',
    height: '',
    weight: '',
    religion: '',
    marital_status: '',
    last_education: '',
    id_card_address: '',
    domicile_address: '',
    residence_ownership_status: '',
    phone_number: '',
    email: '',
    same_as_ktp_domicile: false,

    // Parents
    father_name: '',
    mother_name: '',
    father_occupation: '',
    mother_occupation: '',
    parents_address: '',
    same_as_ktp_parents: false,

    // Spouse
    spouse_name: '',
    spouse_address: '',
    same_as_ktp_spouse: false,
    spouse_age: '',
    spouse_occupation: '',
    spouse_phone: '',

    // Dynamic Data
    work_experiences: [] as any[],
    children: [] as any[],
    emergency_contacts: [] as any[],
    checklist: (props.questions || []).reduce((acc, q) => {
        acc[q.id] = { answer: '', note: '' };
        return acc;
    }, {} as Record<number, { answer: string, note: string }>),

    // Files
    ktp: null as File | null,
    cv: null as File | null,
    photo: null as File | null,
});

const age = ref('');

const calculateAge = (dobString: string) => {
    if (!dobString) return '';
    const today = new Date();
    const birthDate = new Date(dobString);
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age.toString();
};

// Watchers for "Same as KTP"
watch(() => form.same_as_ktp_parents, (val) => {
    if (val) form.parents_address = form.id_card_address;
});

watch(() => form.same_as_ktp_spouse, (val) => {
    if (val) form.spouse_address = form.id_card_address;
});

watch(() => form.same_as_ktp_domicile, (val) => {
    if (val) form.domicile_address = form.id_card_address;
});

// Sync watchers when KTP address changes
watch(() => form.id_card_address, (val) => {
    if (form.same_as_ktp_parents) {
        form.parents_address = val;
    }
    if (form.same_as_ktp_spouse) {
        form.spouse_address = val;
    }
    if (form.same_as_ktp_domicile) {
        form.domicile_address = val;
    }
});

watch(() => form.date_of_birth, (val) => {
    age.value = calculateAge(val);
});

// Helper functions for dynamic fields
const addWorkExperience = () => {
    form.work_experiences.push({
        company_name: '',
        company_address: '',
        position: '',
        start_year: '',
        end_year: '',
    });
};

const removeWorkExperience = (index: number) => {
    form.work_experiences.splice(index, 1);
};

const addChild = () => {
    form.children.push({
        name: '',
        age: '',
        gender: '',
        education: '',
    });
};

const removeChild = (index: number) => {
    form.children.splice(index, 1);
};

const addEmergencyContact = () => {
    form.emergency_contacts.push({
        name: '',
        relationship: '',
        phone_number: '',
    });
};

const removeEmergencyContact = (index: number) => {
    form.emergency_contacts.splice(index, 1);
};

const submit = () => {
    // Use hardcoded path instead of route()
    form.post('/apply', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Form Data Pelamar" />

    <PublicLayout>
        <div class="py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto space-y-6">
                
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Form Data Pelamar</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Silakan lengkapi data diri Anda dengan benar.</p>
                </div>

                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Honeypot -->
                    <input type="text" name="website" v-model="form.website" style="display:none" tabindex="-1" autocomplete="off" />
                    
                    <!-- 1. Data Pelamar -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Pribadi <span class="text-red-500">*</span></CardTitle>
                        </CardHeader>
                        <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2 md:col-span-2">
                                <Label for="position_id">Posisi yang Dilamar</Label>
                                <select id="position_id" v-model="form.position_id" :disabled="!!props.position_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                    <option value="" disabled>Pilih Posisi...</option>
                                    <option v-for="pos in positions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                                </select>
                                <p v-if="form.errors.position_id" class="text-red-500 text-xs">{{ form.errors.position_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="nik">No. KTP (NIK)</Label>
                                <Input id="nik" v-model="form.nik" :class="{'border-red-500': form.errors.nik}" placeholder="Contoh: 3201..." />
                                <p v-if="form.errors.nik" class="text-red-500 text-xs">{{ form.errors.nik }}</p>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="full_name">Nama Lengkap</Label>
                                <Input id="full_name" v-model="form.full_name" :class="{'border-red-500': form.errors.full_name}" />
                                <p v-if="form.errors.full_name" class="text-red-500 text-xs">{{ form.errors.full_name }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="nickname">Nama Panggilan</Label>
                                <Input id="nickname" v-model="form.nickname" />
                            </div>

                            <div class="space-y-2">
                                <Label for="gender">Jenis Kelamin</Label>
                                <select id="gender" v-model="form.gender" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                    <option value="" disabled>Pilih...</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <p v-if="form.errors.gender" class="text-red-500 text-xs">{{ form.errors.gender }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="place_of_birth">Tempat Lahir</Label>
                                <Input id="place_of_birth" v-model="form.place_of_birth" />
                                <p v-if="form.errors.place_of_birth" class="text-red-500 text-xs">{{ form.errors.place_of_birth }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="date_of_birth">Tanggal Lahir</Label>
                                <Input id="date_of_birth" type="date" v-model="form.date_of_birth" class="block w-full" />
                                <p v-if="form.errors.date_of_birth" class="text-red-500 text-xs">{{ form.errors.date_of_birth }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="height">Tinggi Badan (cm)</Label>
                                <Input id="height" type="number" v-model="form.height" placeholder="Contoh: 170" />
                                <p v-if="form.errors.height" class="text-red-500 text-xs">{{ form.errors.height }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="weight">Berat Badan (kg)</Label>
                                <Input id="weight" type="number" v-model="form.weight" placeholder="Contoh: 65" />
                                <p v-if="form.errors.weight" class="text-red-500 text-xs">{{ form.errors.weight }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label>Usia</Label>
                                <Input :value="age" readonly class="bg-gray-100 dark:bg-gray-800 cursor-not-allowed" placeholder="Otomatis" />
                            </div>

                            <div class="space-y-2">
                                <Label for="religion">Agama</Label>
                                <select id="religion" v-model="form.religion" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                    <option value="" disabled>Pilih...</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <p v-if="form.errors.religion" class="text-red-500 text-xs">{{ form.errors.religion }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="marital_status">Status Pernikahan</Label>
                                <select id="marital_status" v-model="form.marital_status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                    <option value="" disabled>Pilih...</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                    <option value="Menikah">Menikah</option>
                                    <option value="Cerai">Cerai</option>
                                </select>
                                <p v-if="form.errors.marital_status" class="text-red-500 text-xs">{{ form.errors.marital_status }}</p>
                            </div>
                            
                             <div class="space-y-2">
                                <Label for="last_education">Pendidikan Terakhir</Label>
                                <Input id="last_education" v-model="form.last_education" placeholder="Contoh: S1 Teknik Informatika" />
                                 <p v-if="form.errors.last_education" class="text-red-500 text-xs">{{ form.errors.last_education }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="phone_number">No Handphone</Label>
                                <Input id="phone_number" v-model="form.phone_number" placeholder="08..." />
                                <p v-if="form.errors.phone_number" class="text-red-500 text-xs">{{ form.errors.phone_number }}</p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="email">Email</Label>
                                <Input id="email" type="email" v-model="form.email" />
                                <p v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="id_card_address">Alamat KTP</Label>
                                <textarea id="id_card_address" v-model="form.id_card_address" class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white dark:border-gray-700"></textarea>
                                <p v-if="form.errors.id_card_address" class="text-red-500 text-xs">{{ form.errors.id_card_address }}</p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <div class="flex items-center space-x-2 mb-2">
                                    <Label for="domicile_address">Alamat Domisili</Label>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" id="same_as_ktp_domicile" v-model="form.same_as_ktp_domicile" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                        <label for="same_as_ktp_domicile" class="text-sm text-gray-600 dark:text-gray-400">Sama dengan KTP</label>
                                    </div>
                                </div>
                                <textarea id="domicile_address" v-model="form.domicile_address" :disabled="form.same_as_ktp_domicile" class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white dark:border-gray-700"></textarea>
                                <p v-if="form.errors.domicile_address" class="text-red-500 text-xs">{{ form.errors.domicile_address }}</p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="residence_ownership_status">Status Kepemilikan Tempat Tinggal</Label>
                                 <select id="residence_ownership_status" v-model="form.residence_ownership_status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                    <option value="" disabled>Pilih...</option>
                                    <option value="Rumah Sendiri">Rumah Sendiri</option>
                                    <option value="Rumah Orang Tua">Rumah Orang Tua</option>
                                    <option value="Rumah Mertua">Rumah Mertua</option>
                                    <option value="Rumah Saudara">Rumah Saudara</option>
                                    <option value="Kontrak/Kos">Kontrak/Kos</option>
                                </select>
                                 <p v-if="form.errors.residence_ownership_status" class="text-red-500 text-xs">{{ form.errors.residence_ownership_status }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- 1.b Dokumen Pendukung -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Dokumen Pendukung <span class="text-red-500">*</span></CardTitle>
                        </CardHeader>
                        <CardContent class="grid grid-cols-1 gap-4">
                            <div class="space-y-2">
                                <Label for="photo">Pas Foto (JPG/JPEG, Max 2MB)</Label>
                                <Input id="photo" type="file" accept=".jpg,.jpeg" @input="form.photo = $event.target.files[0]" />
                                <p v-if="form.errors.photo" class="text-red-500 text-xs">{{ form.errors.photo }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="ktp">Scan KTP (PDF, Max 2MB)</Label>
                                <Input id="ktp" type="file" accept=".pdf" @input="form.ktp = $event.target.files[0]" />
                                <p v-if="form.errors.ktp" class="text-red-500 text-xs">{{ form.errors.ktp }}</p>
                            </div>
                             <div class="space-y-2">
                                <Label for="cv">Curriculum Vitae (PDF, Max 2MB)</Label>
                                <Input id="cv" type="file" accept=".pdf" @input="form.cv = $event.target.files[0]" />
                                <p v-if="form.errors.cv" class="text-red-500 text-xs">{{ form.errors.cv }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- 2. Data Keluarga -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Keluarga <span class="text-red-500">*</span></CardTitle>
                        </CardHeader>
                        <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="father_name">Nama Ayah</Label>
                                <Input id="father_name" v-model="form.father_name" />
                                 <p v-if="form.errors.father_name" class="text-red-500 text-xs">{{ form.errors.father_name }}</p>
                            </div>
                             <div class="space-y-2">
                                <Label for="mother_name">Nama Ibu</Label>
                                <Input id="mother_name" v-model="form.mother_name" />
                                 <p v-if="form.errors.mother_name" class="text-red-500 text-xs">{{ form.errors.mother_name }}</p>
                            </div>
                             <div class="space-y-2">
                                <Label for="father_occupation">Pekerjaan Ayah</Label>
                                <Input id="father_occupation" v-model="form.father_occupation" />
                            </div>
                             <div class="space-y-2">
                                <Label for="mother_occupation">Pekerjaan Ibu</Label>
                                <Input id="mother_occupation" v-model="form.mother_occupation" />
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <div class="flex items-center space-x-2 mb-2">
                                    <Label for="parents_address">Alamat Orang Tua</Label>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" id="same_as_ktp_parents" v-model="form.same_as_ktp_parents" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                        <label for="same_as_ktp_parents" class="text-sm text-gray-600 dark:text-gray-400">Sama dengan KTP</label>
                                    </div>
                                </div>
                                <textarea id="parents_address" v-model="form.parents_address" :disabled="form.same_as_ktp_parents" class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white dark:border-gray-700"></textarea>
                            </div>

                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-medium mb-3">Data Suami/Istri (Jika Ada)</h3>
                            </div>

                            <div class="space-y-2">
                                <Label for="spouse_name">Nama Suami/Istri</Label>
                                <Input id="spouse_name" v-model="form.spouse_name" />
                            </div>
                             <div class="space-y-2">
                                <Label for="spouse_phone">No HP Suami/Istri</Label>
                                <Input id="spouse_phone" v-model="form.spouse_phone" />
                            </div>
                            <div class="space-y-2">
                                <Label for="spouse_age">Usia</Label>
                                <Input id="spouse_age" type="number" v-model="form.spouse_age" />
                            </div>
                            <div class="space-y-2">
                                <Label for="spouse_occupation">Pekerjaan</Label>
                                <Input id="spouse_occupation" v-model="form.spouse_occupation" />
                            </div>
                             <div class="space-y-2 md:col-span-2">
                                 <div class="flex items-center space-x-2 mb-2">
                                    <Label for="spouse_address">Alamat Suami/Istri</Label>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" id="same_as_ktp_spouse" v-model="form.same_as_ktp_spouse" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                        <label for="same_as_ktp_spouse" class="text-sm text-gray-600 dark:text-gray-400">Sama dengan KTP</label>
                                    </div>
                                </div>
                                <textarea id="spouse_address" v-model="form.spouse_address" :disabled="form.same_as_ktp_spouse" class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white dark:border-gray-700"></textarea>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- 3. Pengalaman Kerja -->
                    <Card>
                         <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle>Pengalaman Kerja <span class="text-red-500">*</span></CardTitle>
                            <Button type="button" variant="outline" size="sm" @click="addWorkExperience">
                                + Tambah
                            </Button>
                        </CardHeader>
                        <CardContent>
                             <div v-if="form.work_experiences.length === 0" class="text-center text-gray-500 py-4">
                                 Belum ada data pengalaman kerja. Klik Tambah jika ada.
                             </div>
                             <div v-else class="space-y-4">
                                 <div v-for="(work, index) in form.work_experiences" :key="index" class="p-4 border rounded-lg space-y-3 relative">
                                     <button type="button" @click="removeWorkExperience(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                     
                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                         <div class="space-y-1">
                                             <Label>Nama Perusahaan</Label>
                                             <Input v-model="work.company_name" placeholder="PT..." />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>Posisi / Bagian</Label>
                                             <Input v-model="work.position" />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>Tahun Masuk</Label>
                                             <Input v-model="work.start_year" placeholder="2020" />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>Tahun Keluar</Label>
                                             <Input v-model="work.end_year" placeholder="2022 / Sekarang" />
                                         </div>
                                          <div class="space-y-1 md:col-span-2">
                                             <Label>Alamat Perusahaan</Label>
                                             <Input v-model="work.company_address" />
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </CardContent>
                    </Card>

                    <!-- 4. Data Anak -->
                    <Card>
                         <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle>Data Anak</CardTitle>
                            <Button type="button" variant="outline" size="sm" @click="addChild">
                                + Tambah
                            </Button>
                        </CardHeader>
                        <CardContent>
                             <div v-if="form.children.length === 0" class="text-center text-gray-500 py-4">
                                 Belum ada data anak.
                             </div>
                             <div v-else class="space-y-4">
                                  <div v-for="(child, index) in form.children" :key="index" class="p-4 border rounded-lg space-y-3 relative">
                                     <button type="button" @click="removeChild(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                     <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                          <div class="space-y-1 md:col-span-2">
                                             <Label>Nama</Label>
                                             <Input v-model="child.name" />
                                         </div>
                                         <div class="space-y-1">
                                             <Label>Umur</Label>
                                             <Input type="number" v-model="child.age" />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>Gender</Label>
                                              <select v-model="child.gender" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                         </div>
                                         <div class="space-y-1 md:col-span-4">
                                             <Label>Pendidikan</Label>
                                             <Input v-model="child.education" />
                                         </div>
                                     </div>
                                 </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- 5. Kontak Darurat -->
                    <Card>
                         <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle>Kontak Darurat <span class="text-red-500">*</span></CardTitle>
                            <Button type="button" variant="outline" size="sm" @click="addEmergencyContact">
                                + Tambah
                            </Button>
                        </CardHeader>
                        <CardContent>
                             <div v-if="form.emergency_contacts.length === 0" class="text-center text-gray-500 py-4">
                                  Silakan tambahkan kontak darurat.
                             </div>
                             <div v-else class="space-y-4">
                                  <div v-for="(contact, index) in form.emergency_contacts" :key="index" class="p-4 border rounded-lg space-y-3 relative">
                                     <button type="button" @click="removeEmergencyContact(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                          <div class="space-y-1">
                                             <Label>Nama</Label>
                                             <Input v-model="contact.name" />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>Hubungan</Label>
                                             <Input v-model="contact.relationship" placeholder="Kerabat, Teman, dll" />
                                         </div>
                                          <div class="space-y-1">
                                             <Label>No HP</Label>
                                             <Input v-model="contact.phone_number" />
                                         </div>
                                      </div>
                                 </div>
                             </div>
                        </CardContent>
                    </Card>

                    <!-- 6. Checklist (Placeholder) -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Daftar Pertanyaan <span class="text-red-500">*</span></CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="question in questions" :key="question.id" class="p-4 border rounded-md bg-gray-50 dark:bg-gray-800">
                                    <p class="mb-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ question.text }}</p>
                                    <div class="namespace-y-2">
                                        <div class="flex items-center space-x-4 mb-2">
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" :id="`${question.id}_yes`" value="Ya" v-model="form.checklist[question.id].answer" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                                <label :for="`${question.id}_yes`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Ya</label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" :id="`${question.id}_no`" value="Tidak" v-model="form.checklist[question.id].answer" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                                <label :for="`${question.id}_no`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tidak</label>
                                            </div>
                                        </div>
                                        <div v-if="form.checklist[question.id].answer === 'Ya'">
                                            <Input v-model="form.checklist[question.id].note" placeholder="Berikan keterangan..." class="mt-2" :class="{'border-red-500': form.errors[`checklist.${question.id}.note`]}" />
                                            <p v-if="form.errors[`checklist.${question.id}.note`]" class="text-red-500 text-xs mt-1">{{ form.errors[`checklist.${question.id}.note`] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="form.processing" size="lg">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Data Pelamar' }}
                        </Button>
                    </div>

                </form>
            </div>
        </div>
    </PublicLayout>
</template>
