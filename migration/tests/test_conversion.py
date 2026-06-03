"""Unit tests for HTML->Markdown conversion and media-URL normalization.

These define the contract of the `wp_rest_to_hugo` extractor module
(Constitution V: lossless, verifiable content). Written test-first: they FAIL
until the module is implemented in Phase 2.
"""
import pytest

wp = pytest.importorskip(
    "wp_rest_to_hugo",
    reason="extractor not implemented yet (Phase 2) — RED until then",
)


# --- media URL normalization -------------------------------------------------
@pytest.mark.parametrize("url,expected", [
    ("https://gadjoy.in/wp-content/uploads/2025/02/image-32.png",
     "/img/uploads/2025/02/image-32.png"),
    ("http://localhost:8080/wp-content/uploads/2023/07/4387-Before.mp4",
     "/img/uploads/2023/07/4387-Before.mp4"),
    ("http://gadjoy.in/wp-content/uploads/2022/02/Code-1-236x300.png",
     "/img/uploads/2022/02/Code-1-236x300.png"),
    ("/img/uploads/2025/02/already.png", "/img/uploads/2025/02/already.png"),
    ("https://example.com/external.png", "https://example.com/external.png"),
])
def test_normalize_media_url(url, expected):
    assert wp.normalize_media_url(url) == expected


# --- single image ------------------------------------------------------------
def test_single_image_becomes_markdown_image():
    html = ('<figure class="wp-block-image size-large"><img loading="lazy" '
            'width="1024" height="556" '
            'src="https://gadjoy.in/wp-content/uploads/2025/02/image-32-1024x556.png" '
            'alt="" srcset="http://localhost:8080/wp-content/uploads/2025/02/image-32-1024x556.png 1024w" '
            'sizes="(max-width: 1024px) 100vw, 1024px" /></figure>')
    md = wp.html_to_markdown(html)
    assert "![" in md and "/img/uploads/2025/02/image-32-1024x556.png" in md
    for bad in ("srcset", "wp-block", "gadjoy.in", "localhost:8080", "loading="):
        assert bad not in md


def test_heading_converts_to_atx():
    md = wp.html_to_markdown('<h2 class="wp-block-heading">Before</h2>')
    assert "## Before" in md


def test_unordered_list_converts():
    md = wp.html_to_markdown("<ul><li>Windows terminal</li><li>VS code</li></ul>")
    assert "Windows terminal" in md and "VS code" in md
    # rendered as a markdown bullet list
    assert any(line.lstrip().startswith(("-", "*")) for line in md.splitlines())


def test_video_block_is_preserved_and_normalized():
    html = ('<figure class="wp-block-video aligncenter"><video controls '
            'src="http://gadjoy.in/wp-content/uploads/2023/07/4387-Before.mp4">'
            '</video><figcaption><strong>logo restarting condition</strong>'
            '</figcaption></figure>')
    md = wp.html_to_markdown(html)
    assert "/img/uploads/2023/07/4387-Before.mp4" in md   # video preserved
    assert "logo restarting condition" in md              # caption preserved
    assert "gadjoy.in" not in md


def test_prune_missing_media_drops_only_dangling_refs(tmp_path):
    uploads = tmp_path / "img" / "uploads" / "2023" / "04"
    uploads.mkdir(parents=True)
    (uploads / "exists.png").write_bytes(b"x")
    md = ("![](/img/uploads/2023/04/exists.png)\n\n"
          "![](/img/uploads/2023/04/gone.png)\n\n"
          '<video controls src="/img/uploads/2023/04/gone.mp4"></video>\n')
    out, missing = wp.prune_missing_media(md, uploads_dir=tmp_path / "img" / "uploads")
    assert "/img/uploads/2023/04/exists.png" in out      # kept
    assert "gone.png" not in out and "gone.mp4" not in out  # dropped
    assert set(missing) == {"/img/uploads/2023/04/gone.png", "/img/uploads/2023/04/gone.mp4"}


def test_no_wp_artifacts_survive():
    html = ("<!-- wp:paragraph --><p>Hello [caption id=1]x[/caption]</p>"
            "<!-- /wp:paragraph -->")
    md = wp.html_to_markdown(html)
    assert "<!-- wp:" not in md
    assert "[caption" not in md
    assert "Hello" in md
