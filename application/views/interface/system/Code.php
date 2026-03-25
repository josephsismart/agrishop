<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8f9fa;
            color: #1a1a2e;
            line-height: 1.7;
        }

        /* ── Cover ── */
        .cover {
            background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 50%, #40916c 100%);
            color: #fff;
            padding: 60px 48px;
            text-align: center;
        }

        .cover h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .cover .sub {
            font-size: 16px;
            opacity: .85;
            margin-bottom: 24px;
        }

        .cover .tags {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cover .tag {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 20px;
            padding: 5px 16px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ── Layout ── */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* ── Feature section ── */
        .feature {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, .07);
            margin-bottom: 40px;
            overflow: hidden;
        }

        .feature-header {
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid #f0f0f0;
        }

        .feature-num {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .feature-header h2 {
            font-size: 19px;
            font-weight: 700;
        }

        .feature-header p {
            font-size: 13px;
            color: #6c757d;
            margin-top: 2px;
        }

        /* ── Snippet block ── */
        .snippet {
            padding: 24px 28px;
            border-bottom: 1px solid #f5f5f5;
        }

        .snippet:last-child {
            border-bottom: none;
        }

        .snippet-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 6px;
        }

        .snippet-desc {
            font-size: 14px;
            color: #444;
            margin-bottom: 14px;
            line-height: 1.65;
        }

        .snippet-desc strong {
            color: #1a1a2e;
        }

        pre {
            background: #0f1117;
            color: #e6edf3;
            border-radius: 10px;
            padding: 18px 20px;
            font-size: 12.5px;
            line-height: 1.6;
            overflow-x: auto;
            font-family: 'Consolas', 'Courier New', monospace;
            white-space: pre;
        }

        /* syntax highlight */
        .kw {
            color: #ff7b72;
        }

        /* keywords: function, public, SELECT, FROM */
        .fn {
            color: #d2a8ff;
        }

        /* function names */
        .str {
            color: #a5d6ff;
        }

        /* strings */
        .cm {
            color: #8b949e;
            font-style: italic;
        }

        /* comments */
        .nb {
            color: #79c0ff;
        }

        /* numbers / constants */
        .op {
            color: #ff7b72;
        }

        /* operators */

        .callout {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            border-radius: 0 8px 8px 0;
            padding: 10px 16px;
            font-size: 13px;
            color: #166534;
            margin-top: 12px;
        }

        .callout strong {
            font-weight: 700;
        }

        /* ── Divider ── */
        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #9ca3af;
            margin: 8px 0 4px;
        }

        /* ── Footer ── */
        footer {
            text-align: center;
            padding: 32px;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- COVER -->
    <div class="cover">
        <div style="font-size:40px;margin-bottom:14px;">🌾</div>
        <h1>AgriShop — Source Code Snippets</h1>
        <p class="sub">Relevant code extracts for the 3 core system features</p>
        <div class="tags">
            <span class="tag">🛒 Product Commercialization</span>
            <span class="tag">📍 Geo-Mapping</span>
            <span class="tag">📊 Analytics</span>
        </div>
        <div style="font-size:12px;margin-top:20px;opacity:.6;">
            Framework: CodeIgniter 3 &nbsp;|&nbsp; Stack: PHP · MySQL · Leaflet.js · Chart.js
        </div>
    </div>

    <div class="container">


        <!-- ══════════════════════════════════════════════════════
     FEATURE 1 — PRODUCT COMMERCIALIZATION
══════════════════════════════════════════════════════ -->
        <div class="feature">
            <div class="feature-header">
                <div class="feature-num" style="background:#22c55e;">1</div>
                <div>
                    <h2>🛒 Product Commercialization — Buying &amp; Selling</h2>
                    <p>How products are listed, added to cart, checked out, and fulfilled as orders</p>
                </div>
            </div>

            <!-- SNIPPET 1A -->
            <div class="snippet">
                <div class="snippet-title" style="color:#22c55e;">Snippet 1A — Add to Cart with Stock Validation</div>
                <div class="snippet-desc">
                    When a buyer clicks <strong>"Order Now"</strong> on a farm produce, this server-side method runs.
                    It first <strong>checks real-time stock</strong> using a live computed view (<code>price_qty_left</code>),
                    then either reuses an existing <strong>PENDING transaction</strong> for that farm or creates a new one,
                    and finally inserts the item into <code>my_cart_farm_produce</code> — supporting both retail and wholesale pricing automatically.
                    <br><br>
                    File: <code>controllers/userpublicmap/Map.php</code>
                </div>
                <pre><span class="kw">public function</span> <span class="fn">add_to_cart</span>()
{
    <span class="cm">// Start DB transaction for data integrity</span>
    $this->db->trans_begin();

    $item      = $this->input->post(<span class="str">"item"</span>);
    $qty       = $this->input->post(<span class="str">"qty"</span>);
    $farm_id   = $item[<span class="str">'farm_id'</span>];
    $person_id = $this->session->agrishop_person_id;

    <span class="cm">// Step 1: Check real-time stock availability</span>
    $price_qty_left = $this->price_qty_left(); <span class="cm">// computed subquery view</span>
    $check = $this->db->query(<span class="str">"
        SELECT pql.qty_left, p.name
        FROM ($price_qty_left) pql
        LEFT JOIN produce p ON pql.produce_id = p.id
        WHERE pql.id = $farm_produce_id LIMIT 1
    "</span>)->row();

    <span class="kw">if</span> ($check->qty_left < $qty) {
        echo json_encode([<span class="str">"success"</span> => <span class="nb">false</span>,
            <span class="str">"message"</span> => <span class="str">"Only {$check->qty_left} left for {$check->name}!"</span>]);
        <span class="kw">return</span>;
    }

    <span class="cm">// Step 2: Reuse existing PENDING cart or create a new transaction</span>
    $existing = $this->db->query(<span class="str">"
        SELECT * FROM transaction
        WHERE person_id = $person_id
          AND is_done = false AND farm_id = $farm_id
        LIMIT 1
    "</span>)->row();

    $transaction_id = $existing
        ? $existing->id
        : $this->_createTransaction($person_id, $farm_id);

    <span class="cm">// Step 3: Check wholesale threshold, compute sub_total</span>
    $wholesale = $this->db->query(<span class="str">"
        SELECT * FROM ($price_qty_left) pql
        WHERE id = $farm_produce_id
          AND pql.wholesale_at_qty <= $qty
    "</span>)->row();

    $data_cart = [
        <span class="str">'transaction_id'</span>          => $transaction_id,
        <span class="str">'farm_produce_id'</span>         => $farm_produce_id,
        <span class="str">'price_id_during_transact'</span> => $latest_price_id,
        <span class="str">'qty'</span>                      => $qty,
        <span class="str">'is_wholesale'</span>             => $wholesale ? <span class="nb">true</span> : <span class="nb">false</span>,
        <span class="str">'sub_total'</span>                => $wholesale
            ? $wholesale->price_wholesale * $qty   <span class="cm">// wholesale price</span>
            : $item[<span class="str">'price'</span>] * $qty,            <span class="cm">// retail price</span>
    ];

    $this->db->insert(<span class="str">"my_cart_farm_produce"</span>, $data_cart);
    $this->db->trans_commit();
    echo json_encode([<span class="str">"success"</span> => <span class="nb">true</span>, <span class="str">"message"</span> => <span class="str">"Added to cart!"</span>]);
}</pre>
                <div class="callout">
                    <strong>Key logic:</strong> The system never stores a fixed stock count — it computes
                    <code>qty_left</code> live from harvested quantities minus all sold quantities,
                    so stock is always accurate at the moment of purchase.
                </div>
            </div>

            <!-- SNIPPET 1B -->
            <div class="snippet">
                <div class="snippet-title" style="color:#22c55e;">Snippet 1B — Checkout &amp; Order Submission</div>
                <div class="snippet-desc">
                    When the buyer confirms payment, this method <strong>validates stock one final time</strong>
                    (race condition protection), records the payment method (Cash or GCash),
                    sets the delivery method (Pickup or COD), and transitions the transaction from
                    <strong>PENDING → RESERVED</strong>, notifying the farmer automatically.
                    <br><br>
                    File: <code>controllers/userpublicmap/Map.php</code>
                </div>
                <pre><span class="kw">public function</span> <span class="fn">submit_order</span>()
{
    $pay            = $this->input->post(<span class="str">'pay'</span>);       <span class="cm">// 'cash' or 'gcash'</span>
    $delivery       = $this->input->post(<span class="str">'delivery'</span>);  <span class="cm">// 'pickup' or 'cod'</span>
    $transaction_id = $this->input->post(<span class="str">'trans_id'</span>);
    $subtotal       = $this->input->post(<span class="str">'subtotal'</span>);

    <span class="cm">// Final stock check — prevents over-selling under concurrent orders</span>
    $check_qty = $this->db->query(<span class="str">"
        SELECT pql.qty_left, qq.qty_to_be_checkout, p.name
        FROM ($price_qty_left) pql
        JOIN (
            SELECT farm_produce_id, SUM(qty) AS qty_to_be_checkout
            FROM my_cart_farm_produce
            WHERE transaction_id = $transaction_id
            GROUP BY farm_produce_id
        ) qq ON pql.id = qq.farm_produce_id
             AND qq.qty_to_be_checkout > pql.qty_left
        JOIN produce p ON pql.produce_id = p.id
    "</span>)->result();

    <span class="kw">if</span> (!empty($check_qty)) {
        echo json_encode([<span class="str">"success"</span> => <span class="nb">false</span>,
            <span class="str">"message"</span> => <span class="str">"Stock changed — please review your cart."</span>]);
        <span class="kw">return</span>;
    }

    <span class="cm">// Set delivery status based on buyer's choice</span>
    $delivery_status = ($delivery == <span class="str">'pickup'</span>) ? <span class="str">'TO_BE_PICKUP'</span> : <span class="str">'TO_BE_DELIVER'</span>;

    <span class="cm">// Set payment status based on method</span>
    $payment_status  = ($pay == <span class="str">'gcash'</span>) ? <span class="str">'VERIFYING'</span> : <span class="str">'UNPAID'</span>;

    <span class="cm">// Transition: PENDING → RESERVED</span>
    $this->transaction_status([
        <span class="str">'transaction_id'</span>       => $transaction_id,
        <span class="str">'status'</span>               => <span class="str">'RESERVED'</span>,
        <span class="str">'created_by_person_id'</span> => $person_id,
    ]);

    <span class="cm">// Record checkout details with 1% platform fee</span>
    $this->db->insert(<span class="str">"transaction_details"</span>, [
        <span class="str">'transaction_id'</span>  => $transaction_id,
        <span class="str">'payment_method'</span>  => $pay,
        <span class="str">'delivery_method'</span> => $delivery,
        <span class="str">'total_payment'</span>   => $subtotal * <span class="nb">1.01</span>,   <span class="cm">// includes 1% platform fee</span>
        <span class="str">'to_farmer'</span>       => $subtotal,
        <span class="str">'to_admin'</span>        => $subtotal * <span class="nb">0.01</span>,
    ]);

    echo json_encode([<span class="str">"success"</span> => <span class="nb">true</span>, <span class="str">"message"</span> => <span class="str">"Order confirmed!"</span>]);
}</pre>
                <div class="callout">
                    <strong>Business rule:</strong> The platform collects a 1% convenience fee from each transaction.
                    <code>to_farmer</code> = subtotal, <code>to_admin</code> = 1% of subtotal — this is tracked
                    separately for transparent farmer remittance reporting.
                </div>
            </div>

            <!-- SNIPPET 1C -->
            <div class="snippet">
                <div class="snippet-title" style="color:#22c55e;">Snippet 1C — Frontend Add to Cart (JavaScript)</div>
                <div class="snippet-desc">
                    The buyer-side cart interaction. When a user selects a quantity and clicks Order,
                    this JS function posts the item data to the backend, then <strong>updates the cart badge</strong>
                    in the navbar in real time without a page reload.
                    <br><br>
                    File: <code>views/interface/system/layout/cart_script.php</code>
                </div>
                <pre><span class="kw">function</span> <span class="fn">add_to_cart</span>(item) {
    <span class="cm">// Redirect to login if not authenticated</span>
    <span class="kw">if</span> (!<span class="str">"<?= $this->session->agrishop_login_uname ?>"</span>) {
        window.location.href = <span class="str">"<?= base_url('login') ?>"</span>;
        <span class="kw">return</span>;
    }

    $.post(<span class="str">"<?= base_url('userpublicmap/Map/add_to_cart') ?>"</span>, {
        item: item,
        qty:  $(<span class="str">"#qty"</span> + item.id_).val()
    }, <span class="kw">function</span>(res) {
        <span class="kw">let</span> j = JSON.parse(res);

        <span class="kw">if</span> (j.success) {
            successAlert(j.message);
            <span class="cm">// Live cart badge update — no page refresh needed</span>
            $(<span class="str">".pending-order"</span>).text(j.cart_pending);
        } <span class="kw">else</span> {
            failAlert(j.message); <span class="cm">// e.g. "Not enough stock!"</span>
        }
    });
}</pre>
                <div class="callout">
                    <strong>UX note:</strong> The cart count in the navigation bar updates instantly after adding
                    any item. The server returns <code>cart_pending</code> — the current count of PENDING
                    transactions — which is pushed directly into the badge.
                </div>
            </div>

        </div><!-- /feature 1 -->


        <!-- ══════════════════════════════════════════════════════
     FEATURE 2 — GEO-MAPPING
══════════════════════════════════════════════════════ -->
        <div class="feature">
            <div class="feature-header">
                <div class="feature-num" style="background:#3b82f6;">2</div>
                <div>
                    <h2>📍 Geo-Mapping — Location-Based Access</h2>
                    <p>How farms are discovered on the map, how distances are computed, and how produce pins are placed</p>
                </div>
            </div>

            <!-- SNIPPET 2A -->
            <div class="snippet">
                <div class="snippet-title" style="color:#3b82f6;">Snippet 2A — Produce Search with Farm Location Query</div>
                <div class="snippet-desc">
                    When a buyer types a produce name in the map search bar (e.g. <em>"tomato"</em>),
                    this query finds all farms that have that produce available with stock remaining.
                    It <strong>groups produce per farm</strong> using MySQL's <code>GROUP_CONCAT + JSON_OBJECT</code>,
                    returning everything needed to render a map pin in a single query — farm location (lat/lon),
                    farmer info, and a JSON array of matching produce with prices.
                    <br><br>
                    File: <code>controllers/userpublicmap/Map.php</code>
                </div>
                <pre><span class="kw">function</span> <span class="fn">searchProduce</span>()
{
    $value = $this->input->post(<span class="str">"value"</span>); <span class="cm">// e.g. "tomato"</span>
    $price_qty_left = $this->price_qty_left(); <span class="cm">// live stock subquery</span>

    $results = $this->db->query(<span class="str">"
        SELECT
            fp.farm_id,
            ff.farm_name,
            ff.lat,              -- GPS latitude of the farm
            ff.lon,              -- GPS longitude of the farm
            ff.barangay_id,
            CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
            p.contact_num,

            -- Build a JSON array of matching produce for this farm
            CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'name',            fp.produce_name,
                    'price',           fp.price,
                    'uom',             fp.uom,
                    'qty_left',        fp.qty_left,
                    'img_path',        fp.produce_img_path,
                    'wholesale_price', fp.price_wholesale,
                    'wholesale_at_qty',fp.wholesale_at_qty
                )
                ORDER BY fp.harvest_schedule
            ), ']') AS produce

        FROM (
            -- Subquery: live stock with search filter
            SELECT t11.*, t22.name AS produce_name, t22.img_path AS produce_img_path
            FROM ($price_qty_left) t11
            JOIN produce t22 ON t11.produce_id = t22.id
            WHERE CONCAT(t22.name, t22.tags)
                  COLLATE utf8mb4_general_ci LIKE '%$value%'
        ) fp
        LEFT JOIN farmer_farm ff ON fp.farm_id   = ff.id
        LEFT JOIN farmer       f  ON ff.farmer_id = f.id
        LEFT JOIN person       p  ON f.person_id  = p.id
        GROUP BY fp.farm_id, ff.lat, ff.lon, farmer_name
    "</span>)->result();

    echo json_encode($results);
}</pre>
                <div class="callout">
                    <strong>Why one query?</strong> Using <code>GROUP_CONCAT(JSON_OBJECT(...))</code> lets the
                    system fetch an entire farm's produce list in a single round trip to the database,
                    instead of one query per farm. This keeps the map search fast even with many farms.
                </div>
            </div>

            <!-- SNIPPET 2B -->
            <div class="snippet">
                <div class="snippet-title" style="color:#3b82f6;">Snippet 2B — Leaflet Map Pin with Produce Image Icon</div>
                <div class="snippet-desc">
                    Each farm search result is placed on the map as a <strong>custom circular pin</strong>
                    showing the farm's actual produce image — not a generic marker. The pin is built
                    using Leaflet's <code>L.divIcon</code> with an HTML image element, giving it the
                    circular green-bordered look with a directional pointer.
                    <br><br>
                    File: <code>views/interface/system/layout/map.php</code>
                </div>
                <pre><span class="kw">function</span> <span class="fn">addMarker</span>(farm) {
    <span class="kw">var</span> lat = parseFloat(farm.lat);
    <span class="kw">var</span> lon = parseFloat(farm.lon);

    <span class="cm">// Parse the produce JSON array from the server</span>
    <span class="kw">var</span> produceList = JSON.parse(farm.produce);

    <span class="cm">// Use the first produce's image as the pin icon</span>
    <span class="kw">var</span> pinImgUrl = (produceList.length > <span class="nb">0</span> && produceList[<span class="nb">0</span>].img_path)
        ? produceList[<span class="nb">0</span>].img_path
        : window._mapFallbackImg;

    <span class="cm">// Build a custom circular icon with a green border + pointer</span>
    <span class="kw">var</span> pinIcon = L.divIcon({
        className: <span class="str">''</span>,
        html: <span class="str">'&lt;div style="position:relative;width:52px;"&gt;'</span>
            + <span class="str">'  &lt;div style="width:48px;height:48px;border-radius:50%;overflow:hidden;'</span>
            + <span class="str">'    border:3px solid #28a745;box-shadow:0 3px 8px rgba(0,0,0,.35);"&gt;'</span>
            + <span class="str">'    &lt;img src="'</span> + pinImgUrl + <span class="str">'" width="48" height="48"'</span>
            + <span class="str">'      style="object-fit:cover;"'</span>
            + <span class="str">'      onerror="this.src=window._mapFallbackImg"/&gt;'</span>
            + <span class="str">'  &lt;/div&gt;'</span>
            + <span class="str">'  &lt;!-- Downward pointer triangle --&gt;'</span>
            + <span class="str">'  &lt;div style="width:0;height:0;border-left:10px solid transparent;'</span>
            + <span class="str">'    border-right:10px solid transparent;border-top:12px solid #28a745;'</span>
            + <span class="str">'    margin:0 auto;"&gt;&lt;/div&gt;'</span>
            + <span class="str">'&lt;/div&gt;'</span>,
        iconSize:    [<span class="nb">52</span>, <span class="nb">62</span>],
        iconAnchor:  [<span class="nb">26</span>, <span class="nb">62</span>],   <span class="cm">// tip of pointer = farm's coordinates</span>
        popupAnchor: [<span class="nb">0</span>,  <span class="nb">-65</span>],
    });

    <span class="cm">// Place marker on map and bind popup with produce details</span>
    <span class="kw">var</span> marker = L.marker([lat, lon], { icon: pinIcon }).addTo(map);
    marker.bindPopup(<span class="str">`&lt;b&gt;${farm.farm_name}&lt;/b&gt;&lt;br&gt;${farm.farm_location}`</span>);
}</pre>
                <div class="callout">
                    <strong>Design decision:</strong> Instead of generic red pins, each farm displays its
                    actual produce photo as the map marker. Buyers can visually identify farms at a glance
                    without clicking — e.g. a tomato image tells them that farm sells tomatoes.
                </div>
            </div>

            <!-- SNIPPET 2C -->
            <div class="snippet">
                <div class="snippet-title" style="color:#3b82f6;">Snippet 2C — Haversine Distance Calculation</div>
                <div class="snippet-desc">
                    To help buyers find the nearest farm, the system calculates the
                    <strong>straight-line distance</strong> between the buyer's GPS location and each farm
                    using the Haversine formula — the standard formula for computing distances on a sphere
                    (Earth's curvature is accounted for). Results are sorted nearest-first.
                    <br><br>
                    File: <code>views/interface/system/layout/map.php</code>
                </div>
                <pre><span class="cm">// Haversine formula — computes distance between two GPS coordinates in km</span>
<span class="kw">function</span> <span class="fn">getDistanceKm</span>(latlng1, latlng2) {
    <span class="kw">var</span> R    = <span class="nb">6371</span>;  <span class="cm">// Earth's radius in kilometers</span>
    <span class="kw">var</span> dLat = (latlng2.lat - latlng1.lat) * Math.PI / <span class="nb">180</span>;
    <span class="kw">var</span> dLon = (latlng2.lng - latlng1.lng) * Math.PI / <span class="nb">180</span>;
    <span class="kw">var</span> a    = Math.sin(dLat/<span class="nb">2</span>) * Math.sin(dLat/<span class="nb">2</span>)
             + Math.cos(latlng1.lat * Math.PI/<span class="nb">180</span>)
             * Math.cos(latlng2.lat * Math.PI/<span class="nb">180</span>)
             * Math.sin(dLon/<span class="nb">2</span>) * Math.sin(dLon/<span class="nb">2</span>);
    <span class="kw">var</span> c    = <span class="nb">2</span> * Math.atan2(Math.sqrt(a), Math.sqrt(<span class="nb">1</span> - a));
    <span class="kw">return</span> R * c;  <span class="cm">// distance in km</span>
}

<span class="cm">// Usage: sort all found farms by distance from the buyer</span>
<span class="kw">function</span> <span class="fn">buildRoutesTable</span>() {
    <span class="kw">var</span> routes = searchedFarms.map(<span class="kw">function</span>(farm) {
        <span class="kw">return</span> {
            name:     farm.farm_name,
            distance: getDistanceKm(userLatLng, {
                lat: parseFloat(farm.lat),
                lng: parseFloat(farm.lon)
            })
        };
    });

    <span class="cm">// Sort nearest farm first</span>
    routes.sort(<span class="kw">function</span>(a, b) { <span class="kw">return</span> a.distance - b.distance; });

    <span class="cm">// Highlight nearest (green) and farthest (red) in the table</span>
    routes.forEach(<span class="kw">function</span>(r, i) {
        <span class="kw">var</span> rowClass = i === <span class="nb">0</span>
            ? <span class="str">"table-success"</span>               <span class="cm">// nearest = green</span>
            : (i === routes.length - <span class="nb">1</span>
                ? <span class="str">"table-danger"</span>               <span class="cm">// farthest = red</span>
                : <span class="str">""</span>);
        <span class="cm">// render row...</span>
    });
}</pre>
                <div class="callout">
                    <strong>Practical use:</strong> After searching for a produce, buyers see a ranked table
                    of farms sorted by distance. The nearest farm is highlighted green, the farthest red.
                    Clicking "View Route" draws a road-based route using Leaflet Routing Machine.
                </div>
            </div>

        </div><!-- /feature 2 -->


        <!-- ══════════════════════════════════════════════════════
     FEATURE 3 — ANALYTICS
══════════════════════════════════════════════════════ -->
        <div class="feature">
            <div class="feature-header">
                <div class="feature-num" style="background:#f97316;">3</div>
                <div>
                    <h2>📊 Analytics — Fast/Slow Products &amp; Insights</h2>
                    <p>How the system identifies top sellers, slow movers, revenue trends, and business insights</p>
                </div>
            </div>

            <!-- SNIPPET 3A -->
            <div class="snippet">
                <div class="snippet-title" style="color:#f97316;">Snippet 3A — Fast &amp; Slow Moving Produce Query</div>
                <div class="snippet-desc">
                    The system ranks all produce by <strong>total quantity sold</strong> across all completed,
                    non-cancelled transactions. The same query result is sliced two ways — the top records
                    become "Fast Moving" and the bottom records become "Slow Moving" — giving farmers and
                    admins actionable insight into which products to push more and which to reprice.
                    <br><br>
                    File: <code>controllers/userfarmer/Dashboard.php</code>
                </div>
                <pre><span class="cm">// One query — sorted DESC gives fast movers, ASC gives slow movers</span>
$products_selling = $this->db->query(<span class="str">"
    SELECT
        p.name                                  AS produce_name,
        COALESCE(p.img_path, pc.img_path)       AS img_path,
        SUM(mcfp.qty)                           AS total_qty_sold,
        pmfp.price                              AS current_price,
        fp.uom

    FROM transaction t
    JOIN farmer_farm ff   ON t.farm_id           = ff.id
    JOIN my_cart_farm_produce mcfp ON t.id       = mcfp.transaction_id
    JOIN farm_produce fp          ON mcfp.farm_produce_id = fp.id
    JOIN produce p                ON fp.produce_id        = p.id
    JOIN price_monitoring_farm_produce pmfp
                                  ON mcfp.price_id_during_transact = pmfp.id
    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
    LEFT JOIN transaction_cancel tc     ON t.id = tc.transaction_id

    WHERE ff.farmer_id = $farmer_id
      AND tc.id IS NULL          -- exclude cancelled orders
      AND p.name IS NOT NULL     -- exclude orphaned rows

    GROUP BY p.id, pmfp.price, fp.uom
    ORDER BY SUM(mcfp.qty) DESC  -- change to ASC for slow movers
"</span>)->result();</pre>

                <pre><span class="cm">// In the view — slice the same array for fast and slow</span>
$top4  = array_slice($p_selling, <span class="nb">0</span>, <span class="nb">4</span>);               <span class="cm">// Fast movers (top 4)</span>
$slow2 = array_slice(array_reverse($p_selling), <span class="nb">0</span>, <span class="nb">2</span>); <span class="cm">// Slow movers (bottom 2)</span></pre>
                <div class="callout">
                    <strong>Insight value:</strong> Slow-moving produce triggers an advisory message —
                    <em>"Consider reducing prices or promoting these items."</em>
                    Fast-moving produce shows a "🔥 Hot" badge to highlight what buyers want most.
                </div>
            </div>

            <!-- SNIPPET 3B -->
            <div class="snippet">
                <div class="snippet-title" style="color:#f97316;">Snippet 3B — Monthly Revenue Trend Query</div>
                <div class="snippet-desc">
                    The revenue trend chart is powered by a SQL query that groups completed transactions
                    by month. It computes <strong>net farmer revenue</strong> (total payment minus admin fee)
                    and <strong>total quantity sold per month</strong> — both series are displayed on a
                    dual-axis Chart.js graph so farmers can see volume vs. income together.
                    <br><br>
                    File: <code>controllers/userfarmer/Dashboard.php</code>
                </div>
                <pre>$current_year = date(<span class="str">'Y'</span>);

$monthly = $this->db->query(<span class="str">"
    SELECT
        DATE_FORMAT(t.transaction_date, '%b') AS month,   -- e.g. 'Jan','Feb'
        SUM(mcfp.qty)                          AS qty,    -- total units sold
        SUM(td.total_payment - td.to_admin)    AS revenue -- net to farmer

    FROM transaction t
    JOIN farmer_farm ff   ON t.farm_id           = ff.id
    JOIN transaction_details td     ON t.id      = td.transaction_id
    JOIN my_cart_farm_produce mcfp  ON t.id      = mcfp.transaction_id
    LEFT JOIN transaction_cancel tc ON t.id      = tc.transaction_id

    WHERE ff.farmer_id = $farmer_id
      AND DATE_FORMAT(t.transaction_date, '%Y') = '$current_year'
      AND tc.id IS NULL                          -- exclude cancelled

    GROUP BY DATE_FORMAT(t.transaction_date, '%b'),
             DATE_FORMAT(t.transaction_date, '%m')
    ORDER BY DATE_FORMAT(t.transaction_date, '%m')  -- Jan=01 → Dec=12
"</span>)->result();

$ordersGraph = json_encode($monthly); <span class="cm">// passed to Chart.js as JSON</span></pre>

                <pre><span class="cm">// Chart.js rendering in the view — dual axis: revenue bars + qty line</span>
<span class="kw">new</span> Chart(document.getElementById(<span class="str">'revenueChart'</span>), {
    type: <span class="str">'bar'</span>,
    data: {
        labels: ordersRaw.map(r => r.month),
        datasets: [
            {
                label: <span class="str">'Revenue (₱)'</span>,
                data:  ordersRaw.map(r => r.revenue || <span class="nb">0</span>),
                backgroundColor: <span class="str">'rgba(34,197,94,.75)'</span>,
                borderRadius: <span class="nb">6</span>,
            },
            {
                label: <span class="str">'Qty Sold'</span>,
                data:  ordersRaw.map(r => r.qty || <span class="nb">0</span>),
                type:  <span class="str">'line'</span>,           <span class="cm">// overlay line on bar chart</span>
                yAxisID: <span class="str">'y1'</span>,          <span class="cm">// separate right-side axis</span>
                borderColor: <span class="str">'#3b82f6'</span>,
                tension: <span class="nb">0.4</span>,
            }
        ]
    }
});</pre>
                <div class="callout">
                    <strong>Business insight:</strong> When quantity sold is high but revenue is low in the same month,
                    it signals the farmer may be underpricing. When revenue is high but quantity is low,
                    they may be selling bulk wholesale. The dual-axis chart makes this pattern visible instantly.
                </div>
            </div>

            <!-- SNIPPET 3C -->
            <div class="snippet">
                <div class="snippet-title" style="color:#f97316;">Snippet 3C — Wholesale vs. Retail Breakdown</div>
                <div class="snippet-desc">
                    This query segments all of a farmer's sales into <strong>Wholesale</strong> and
                    <strong>Retail</strong> transactions by checking whether a wholesale price was recorded
                    at the time of purchase. It gives farmers a clear picture of which buyer type drives
                    their income.
                    <br><br>
                    File: <code>controllers/userfarmer/Dashboard.php</code>
                </div>
                <pre>$wholesale_retail = $this->db->query(<span class="str">"
    SELECT
        CASE
            WHEN pmfp.price_wholesale IS NOT NULL THEN 'WHOLESALE'
            ELSE 'RETAIL'
        END                                    AS sale_type,
        SUM(mcfp.qty)                          AS total_qty,
        SUM(td.total_payment - td.to_admin)    AS total_revenue

    FROM transaction t
    JOIN farmer_farm ff  ON t.farm_id = ff.id
    JOIN transaction_details td    ON t.id = td.transaction_id
    JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
    JOIN price_monitoring_farm_produce pmfp
                                   ON mcfp.price_id_during_transact = pmfp.id
    LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id

    WHERE ff.farmer_id = $farmer_id
      AND DATE_FORMAT(t.transaction_date,'%Y') = '$current_year'
      AND tc.id IS NULL

    GROUP BY
        CASE WHEN pmfp.price_wholesale IS NOT NULL
             THEN 'WHOLESALE' ELSE 'RETAIL' END
"</span>)->result();</pre>
                <div class="callout">
                    <strong>Why this matters:</strong> Farmers who sell mostly wholesale may want to promote
                    retail to increase margins. The system renders this as a <strong>donut chart</strong>
                    split by Wholesale / Retail so the ratio is immediately visible on the dashboard.
                </div>
            </div>

            <!-- SNIPPET 3D -->
            <div class="snippet">
                <div class="snippet-title" style="color:#f97316;">Snippet 3D — Admin Platform-Wide Top Farmers Leaderboard</div>
                <div class="snippet-desc">
                    The admin dashboard shows a <strong>ranked leaderboard</strong> of the top 5 farmers
                    by total revenue earned — useful for identifying star performers, planning incentives,
                    and demonstrating platform impact to stakeholders.
                    <br><br>
                    File: <code>controllers/useradmin/Dashboard.php</code>
                </div>
                <pre>$top_farmers = $this->db->query(<span class="str">"
    SELECT
        COALESCE(
            CONCAT(p.first_name, ' ', p.last_name),
            'Unknown Farmer'
        )              AS farmer,
        SUM(td.to_farmer) AS revenue

    FROM transaction t
    LEFT JOIN farmer_farm ff         ON t.farm_id     = ff.id
    LEFT JOIN transaction_details td ON t.id          = td.transaction_id
    LEFT JOIN transaction_cancel tc  ON t.id          = tc.transaction_id
    LEFT JOIN farmer f               ON ff.farmer_id  = f.id
    LEFT JOIN person p               ON f.person_id   = p.id

    WHERE tc.id IS NULL       -- exclude cancelled orders
      AND p.id  IS NOT NULL   -- exclude orphaned transactions

    GROUP BY CONCAT(p.first_name, ' ', p.last_name)
    ORDER BY SUM(td.to_farmer) DESC
    LIMIT 5
"</span>)->result();</pre>

                <pre><span class="cm">&lt;!-- View: render with medal emojis for top 3 --&gt;</span>
<span class="kw">&lt;?php</span>
$medals = [<span class="str">'🥇'</span>, <span class="str">'🥈'</span>, <span class="str">'🥉'</span>];
$rank   = <span class="nb">0</span>;
<span class="kw">foreach</span> ($top_farmers <span class="kw">as</span> $f): $rank++;
<span class="kw">?&gt;</span>
&lt;div class=<span class="str">"leaderboard-row"</span>&gt;
    &lt;span class=<span class="str">"medal"</span>&gt;<span class="kw">&lt;?=</span> $medals[$rank-<span class="nb">1</span>] ?? $rank <span class="kw">?&gt;</span>&lt;/span&gt;
    &lt;span class=<span class="str">"name"</span>&gt;<span class="kw">&lt;?=</span> htmlspecialchars($f->farmer ?? <span class="str">'—'</span>) <span class="kw">?&gt;</span>&lt;/span&gt;
    &lt;span class=<span class="str">"revenue"</span>&gt;₱<span class="kw">&lt;?=</span> number_format($f->revenue ?? <span class="nb">0</span>, <span class="nb">2</span>) <span class="kw">?&gt;</span>&lt;/span&gt;
&lt;/div&gt;
<span class="kw">&lt;?php</span> <span class="kw">endforeach</span>; <span class="kw">?&gt;</span></pre>
                <div class="callout">
                    <strong>Admin use case:</strong> This leaderboard is visible on the admin dashboard and
                    updates with every completed order. It supports farmer recognition programs and helps
                    the admin identify which farmers need support or promotion.
                </div>
            </div>

        </div><!-- /feature 3 -->

    </div><!-- /container -->

    <footer>
        AgriShop — Farm-to-Table Marketplace &nbsp;|&nbsp; Caraga Region, Philippines<br>
        Built with CodeIgniter 3 · PHP · MySQL · Leaflet.js · Chart.js
    </footer>

</body>

</html>