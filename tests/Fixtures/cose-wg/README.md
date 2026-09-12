# cose-wg/Examples

A subset of the interoperability fixtures of the IETF COSE working group, vendored from
<https://github.com/cose-wg/Examples>.

| | |
|---|---|
| Upstream commit | [`53c9d634333bb4f529d78f5980fffa2667ee2c12`](https://github.com/cose-wg/Examples/tree/53c9d634333bb4f529d78f5980fffa2667ee2c12) (2024-03-13) |
| Licence | [Unlicense](LICENSE) (public domain) |

The files are copied as they are. Only the `output.cbor` member of each one is used here: it is the message in
CBOR, and `tests/Functional/CoseMessagesTest.php` checks that the decoder service of the bundle reads it into the
COSE tag class of the library and writes it back byte for byte. Verifying the signatures and MACs, or decrypting
the messages, is the job of [web-auth/cose-lib](https://github.com/web-auth/cose-lib), which runs every fixture of
the upstream repository.

- `RFC8152/`: the examples of the appendices of [RFC 8152](https://datatracker.ietf.org/doc/html/rfc8152#appendix-C)
  (one message of each COSE type, several of them with more than one recipient or signer).
- `CWT/`: the examples of the appendix of [RFC 8392](https://datatracker.ietf.org/doc/html/rfc8392#appendix-A)
  (CBOR Web Tokens).
