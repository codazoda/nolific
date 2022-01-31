# Contributing

These are personal notes for my own maintenance of this project. Eventually I'll add notes for outside contributers.

**Tagging**

I'm using semver now. The first two versions didn't properly follow the convention but I'll do so starting with v0.3.0 (which is the third minor release).

major.minor.patch

```
git tag -a v0.3.0 -m "New version"
git push --tags
```

**List commits since last tag**

`git log <yourlasttag>..HEAD --oneline`
