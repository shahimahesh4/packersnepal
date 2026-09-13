<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->longText('details')->nullable()->after('description');
            $table->string('seo_title', 70)->nullable()->after('details');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
        });

        $content = [
            'household-packing' => ['Professional Household Packing Services in Nepal', 'Protect household belongings with careful room-by-room packing, wrapping and labeling from Packers Nepal in Kathmandu Valley.', "Household packing should make preparation feel organized, not overwhelming. Our specialists work room by room to protect everyday belongings, kitchenware, decor, books, clothing, electronics, and other agreed items.\n\nWHAT WE CAN PACK\n\nWe can prepare complete rooms or selected groups of belongings. Tell us about delicate, unusually shaped, valuable, or heavy items during the quotation stage so we can review the right approach.\n\nOUR PACKING APPROACH\n\nThe team uses suitable cartons, protective paper, bubble wrap, tape, and clear labels according to the agreed scope. Items are grouped logically to support an organized handover.\n\nREADY FOR YOUR TRANSPORTER\n\nAfter the final check, the packed goods are ready for the transporter you arrange. Transportation and delivery are separate from our packing service."],
            'office-packing' => ['Professional Office Packing Services in Kathmandu', 'Organize office equipment, files and workspaces with professional packing and labeling by Packers Nepal in Kathmandu Valley.', "An organized office packing plan helps protect equipment and reduces confusion during handover. We prepare agreed workstations, documents, electronics, supplies, and shared areas using a clear labeling system.\n\nPLANNED BY AREA\n\nWe can group items by department, room, employee, or equipment type. Your nominated contact can identify priority items and anything that should remain accessible.\n\nPROTECTING OFFICE EQUIPMENT\n\nMonitors, computers, peripherals, files, and other agreed equipment receive packing appropriate to their shape and handling needs.\n\nCLEAR HANDOVER\n\nPacked items are checked and prepared for collection by your chosen transporter. We do not provide vehicle or delivery services."],
            'fragile-packing' => ['Fragile Item Packing Services in Nepal', 'Get careful wrapping and protective packing for glassware, electronics, artwork and delicate belongings from Packers Nepal.', "Fragile belongings need more than an ordinary box. Our team reviews the items involved and plans protective layers that reduce movement, contact, and handling risk during the next stage of their journey.\n\nITEM-SPECIFIC PROTECTION\n\nGlassware, ceramics, electronics, artwork, lamps, decor, and other delicate goods may require individual wrapping, cushioning, dividers, or reinforced cartons.\n\nCAREFUL LABELING\n\nPackages are clearly identified to support careful handling and an organized handover. Please tell us about unusually valuable or sensitive items before accepting the quotation.\n\nPACKING SERVICE ONLY\n\nWe prepare fragile goods at the agreed location. You arrange transportation, insurance, and delivery separately."],
            'business-packing' => ['Business Packing Services for Goods in Nepal', 'Prepare retail stock, products and business goods with organized professional packing from Packers Nepal in Kathmandu Valley.', "Businesses need packing that is consistent, organized, and easy to verify. We help retailers, offices, and organizations prepare agreed goods, products, supplies, and equipment for collection.\n\nPACKING TO YOUR WORKFLOW\n\nItems can be grouped by product, department, destination, batch, or another agreed system. We confirm the scope before work begins.\n\nMATERIALS AND LABELS\n\nOur team uses suitable protective materials and clear labels to support identification and handover. Quantity, access, and any special requirements should be included in your request.\n\nFLEXIBLE TRANSPORT CHOICE\n\nOnce packing is checked, you remain free to select the transporter, collection schedule, and insurance arrangement that suit your business."],
        ];

        foreach ($content as $slug => [$seoTitle, $metaDescription, $details]) {
            DB::table('services')->where('slug', $slug)->update([
                'details' => $details,
                'seo_title' => $seoTitle,
                'meta_description' => $metaDescription,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['details', 'seo_title', 'meta_description']);
        });
    }
};
