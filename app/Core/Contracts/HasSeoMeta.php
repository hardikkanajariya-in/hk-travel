<?php

namespace App\Core\Contracts;

/**
 * Models implement this contract so the SeoManager can pull a normalized
 * meta payload (title, description, og image, JSON-LD) without the model
 * having to know about SeoManager internals.
 */
interface HasSeoMeta
{
    /**
     * @return array{
     *   title?:string,
     *   description?:?string,
     *   image?:?string,
     *   noindex?:bool,
     *   canonical?:?string,
     *   schema?:?array<string,mixed>
     * }
     */
    public function toSeoMeta(): array;
}
