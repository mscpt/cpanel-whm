<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name'      => 'Administrador',
            'email'     => 'admin@webhs.pt',
            'password'  => Hash::make('Admin@Webhs2025!'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $comercial = User::create([
            'name'      => 'Comercial Demo',
            'email'     => 'comercial@webhs.pt',
            'password'  => Hash::make('Comercial@Webhs2025!'),
            'role'      => 'commercial',
            'is_active' => true,
        ]);

        // Default pipeline with 5 stages
        $pipeline = Pipeline::create([
            'name'        => 'Vendas',
            'description' => 'Pipeline principal de vendas',
        ]);

        $stages = [
            ['name' => 'Novo Lead',        'color' => '#6366f1', 'order' => 1],
            ['name' => 'Contactado',        'color' => '#f59e0b', 'order' => 2],
            ['name' => 'Proposta Enviada',  'color' => '#3b82f6', 'order' => 3],
            ['name' => 'Ganho',             'color' => '#10b981', 'order' => 4, 'is_won' => true],
            ['name' => 'Perdido',           'color' => '#ef4444', 'order' => 5, 'is_lost' => true],
        ];

        $createdStages = [];
        foreach ($stages as $stageData) {
            $createdStages[] = PipelineStage::create(array_merge(['pipeline_id' => $pipeline->id], $stageData));
        }

        // Sample clients
        $clients = [
            ['name' => 'Empresa Alpha Lda', 'email' => 'geral@alpha.pt', 'phone' => '+351 210 000 001', 'type' => 'company', 'city' => 'Lisboa'],
            ['name' => 'Beta Solutions SA',  'email' => 'info@beta.pt',   'phone' => '+351 220 000 002', 'type' => 'company', 'city' => 'Porto'],
            ['name' => 'João Silva',          'email' => 'joao@silva.pt',  'phone' => '+351 912 000 003', 'type' => 'person',  'city' => 'Braga'],
            ['name' => 'Gamma Startup Lda',  'email' => 'hello@gamma.pt', 'phone' => '+351 230 000 004', 'type' => 'company', 'city' => 'Coimbra'],
            ['name' => 'Delta Comércio Lda', 'email' => 'delta@delta.pt', 'phone' => '+351 240 000 005', 'type' => 'company', 'city' => 'Faro'],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);
            Contact::create([
                'client_id' => $client->id,
                'name'      => 'Contacto Principal',
                'email'     => $clientData['email'],
                'phone'     => $clientData['phone'],
                'role'      => 'Responsável',
            ]);
        }

        // Sample leads distributed across stages
        $leadData = [
            ['name' => 'Tech Startup XYZ', 'company' => 'XYZ Lda',   'email' => 'xyz@xyz.pt',    'source' => 'website',  'value' => 299, 'stage_index' => 0],
            ['name' => 'Loja Online ABC',   'company' => 'ABC Store', 'email' => 'abc@loja.pt',   'source' => 'referral', 'value' => 599, 'stage_index' => 1],
            ['name' => 'Escritório Modern', 'company' => 'Modern SA', 'email' => 'mod@modern.pt', 'source' => 'whmcs',    'value' => 149, 'stage_index' => 2],
            ['name' => 'Restaurante Bom',   'company' => 'Bom Sabor', 'email' => 'info@bom.pt',   'source' => 'website',  'value' => 99,  'stage_index' => 3],
            ['name' => 'Farmácia Central',  'company' => 'Farmácia',  'email' => 'farm@farm.pt',  'source' => 'referral', 'value' => 199, 'stage_index' => 4],
        ];

        foreach ($leadData as $i => $ld) {
            Lead::create([
                'pipeline_id'       => $pipeline->id,
                'pipeline_stage_id' => $createdStages[$ld['stage_index']]->id,
                'assigned_to'       => $comercial->id,
                'name'              => $ld['name'],
                'company'           => $ld['company'],
                'email'             => $ld['email'],
                'source'            => $ld['source'],
                'value'             => $ld['value'],
                'order'             => $i,
            ]);
        }

        // Settings defaults
        $defaults = [
            'meta_pixel_id'       => '',
            'ga_measurement_id'   => '',
            'gtm_container_id'    => '',
            'custom_head_scripts' => '',
            'custom_body_scripts' => '',
            'company_name'        => 'Webhs',
            'company_logo_url'    => '',
            'company_address'     => '',
            'company_email'       => 'geral@webhs.pt',
            'company_phone'       => '',
        ];
        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
