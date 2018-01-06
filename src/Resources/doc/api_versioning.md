When you already use FOS Rest Bundle you can turn on `VersionedViewlistener` using `versioned_view_listener` set to `true`.
This gives ability to set current API version to `FOS\RestBundle\View\View`'s context.
From now you can use serialization `since-version="1.1"` and `until-version="1.5"` XML attributes when serializing models.
