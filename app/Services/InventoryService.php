<?php

namespace App\Services;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public const REASONS = [
        'stock_received',
        'correction',
        'damaged',
        'return',
        'other',
    ];

    public const MODES = ['add', 'remove', 'set'];

    public function adjust(
        Product $product,
        ?ProductVariant $variant,
        string $mode,
        int $quantity,
        string $reason,
        ?string $note = null,
        ?User $user = null,
    ): ?InventoryAdjustment {
        if (! in_array($mode, self::MODES, true)) {
            throw ValidationException::withMessages(['mode' => __('admin.inventory.invalid_mode')]);
        }

        if ($quantity < 0 || ($mode !== 'set' && $quantity < 1)) {
            throw ValidationException::withMessages(['quantity' => __('admin.inventory.invalid_quantity')]);
        }

        return DB::transaction(function () use ($product, $variant, $mode, $quantity, $reason, $note, $user) {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->getKey());

            $lockedVariant = null;

            if ($variant !== null) {
                $lockedVariant = ProductVariant::query()->lockForUpdate()->findOrFail($variant->getKey());

                if ((int) $lockedVariant->product_id !== (int) $lockedProduct->getKey()) {
                    throw ValidationException::withMessages(['variant_id' => __('admin.inventory.invalid_variant')]);
                }
            } else {
                $hasVariants = ProductVariant::query()->where('product_id', $lockedProduct->getKey())->exists();

                if ($hasVariants) {
                    throw ValidationException::withMessages(['variant_id' => __('admin.inventory.variant_required')]);
                }
            }

            $before = $lockedVariant !== null
                ? (int) $lockedVariant->stock_quantity
                : max(0, (int) ($lockedProduct->stock_quantity ?? 0));

            $after = match ($mode) {
                'add' => $before + $quantity,
                'remove' => $before - $quantity,
                'set' => $quantity,
            };

            if ($after < 0) {
                throw ValidationException::withMessages(['quantity' => __('admin.inventory.negative')]);
            }

            if ($after === $before) {
                return null;
            }

            if ($lockedVariant !== null) {
                $lockedVariant->update(['stock_quantity' => $after]);
            } else {
                $lockedProduct->update(['stock_quantity' => $after]);
            }

            return InventoryAdjustment::query()->create([
                'product_id' => $lockedProduct->getKey(),
                'variant_id' => $lockedVariant?->getKey(),
                'user_id' => $user?->getKey(),
                'quantity_before' => $before,
                'quantity_change' => $after - $before,
                'quantity_after' => $after,
                'reason' => $reason,
                'note' => $note,
            ]);
        });
    }
}
